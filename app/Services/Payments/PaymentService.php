<?php

namespace App\Services\Payments;

use App\Events\PaymentConfirmed;
use App\Models\CommandePharmaceutique;
use App\Models\Facture;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentService
{
    /**
     * Create or return an existing payment using idempotence.
     *
     * @return array{payment: Paiement, created: bool}
     */
    public function createPayment(array $data, ?string $headerIdempotenceKey = null, ?Request $request = null): array
    {
        $facture = isset($data['facture_id']) ? Facture::find($data['facture_id']) : null;
        $commande = isset($data['commande_id']) ? CommandePharmaceutique::find($data['commande_id']) : null;

        if (!$facture && !$commande) {
            abort(422, 'Une facture ou une commande doit être fournie');
        }

        $payeurId = $data['payeur_id']
            ?? $facture?->patient_id
            ?? $commande?->patient_id;

        $idempotenceKey = $data['idempotence_key'] ?? $headerIdempotenceKey ?? (string) Str::uuid();

        if ($existing = Paiement::where('idempotence_key', $idempotenceKey)->first()) {
            Log::info('Paiement idempotent recupere', ['paiement_id' => $existing->id, 'idempotence_key' => $idempotenceKey]);
            return ['payment' => $existing, 'created' => false];
        }

        $payload = array_merge($data, [
            'payeur_id' => $payeurId,
            'type_reference' => $data['type_reference'] ?? ($commande ? 'commande_pharmaceutique' : null),
            'reference_id' => $data['reference_id'] ?? ($commande?->id),
            'numero_paiement' => Paiement::generateNumero(),
            'idempotence_key' => $idempotenceKey,
            'reference_transaction' => $data['reference_transaction'] ?? strtoupper(Str::random(14)),
            'montant_devise_locale' => $data['montant_devise_locale'] ?? round($data['montant'] * ($data['taux_change'] ?? 1), 2),
            'montant_net' => $data['montant_net'] ?? max($data['montant'] - ($data['frais_transaction'] ?? 0), 0),
            'date_initiation' => now(),
            'statut' => $data['statut'] ?? 'initie',
        ]);

        $payment = DB::transaction(function () use ($payload, $facture, $commande, $request) {
            /** @var Paiement $created */
            $created = Paiement::create($payload);
            $facture?->updateRemainingAmount();

            // Calcul des répartitions et cantonnement si commande
            if ($commande && $created->statut === 'confirme') {
                $this->repartirPaiementCommande($commande, $created);
            }

            Log::info('Paiement cree', [
                'paiement_id' => $created->id,
                'facture_id' => $created->facture_id,
                'commande_id' => $created->commande_id,
                'payeur_id' => $created->payeur_id,
                'mode' => $created->mode_paiement,
                'ip' => $request?->ip(),
            ]);

            return $created;
        });

        return ['payment' => $payment, 'created' => true];
    }

    /**
     * Confirm a payment after gateway callback with optional HMAC signature validation.
     */
    public function confirmPayment(Paiement $paiement, array $attributes, ?string $payload = null, ?string $signature = null): Paiement
    {
        if ($paiement->statut === 'confirme') {
            return $paiement;
        }

        $secret = config('services.payments.webhook_secret');
        if ($secret) {
            $expected = hash_hmac('sha256', $payload ?? '', $secret);
            if (!$signature || !hash_equals($expected, $signature)) {
                Log::warning('Signature webhook invalide', ['paiement_id' => $paiement->id]);
                abort(403, 'Signature webhook invalide');
            }
        }

        $updated = DB::transaction(function () use ($paiement, $attributes) {
            $paiement->update([
                'statut' => 'confirme',
                'date_confirmation' => now(),
                'reference_passerelle' => $attributes['reference_passerelle'] ?? $paiement->reference_passerelle,
                'code_autorisation' => $attributes['code_autorisation'] ?? $paiement->code_autorisation,
                'reponse_passerelle' => $attributes['reponse_passerelle'] ?? $paiement->reponse_passerelle,
            ]);

            $paiement->facture?->updateRemainingAmount();
            // Synchroniser le statut de paiement de la commande si elle existe
            $commande = $paiement->commande;
            if (!$commande && $paiement->type_reference === 'commande_pharmaceutique' && $paiement->reference_id) {
                $commande = CommandePharmaceutique::find($paiement->reference_id);
            }
            if ($commande) {
                $commande->majStatutPaiement();
                $this->repartirPaiementCommande($commande, $paiement);
            }

            Log::info('Paiement confirme', [
                'paiement_id' => $paiement->id,
                'facture_id' => $paiement->facture_id,
                'reference_passerelle' => $attributes['reference_passerelle'] ?? null,
            ]);

            return $paiement;
        });

        PaymentConfirmed::dispatch($updated);

        return $updated->fresh();
    }

    /**
     * Calcule et enregistre la répartition (pharmacie/livreur/commission) et le cantonnement.
     */
    protected function repartirPaiementCommande(CommandePharmaceutique $commande, Paiement $paiement): void
    {
        $commissionPct = config('payments.commission_pct', 0.05);
        $livreurPctLivraison = config('payments.livreur_pct_delivery', 0.8);

        $fraisLivraison = (float) ($commande->frais_livraison ?? 0);
        $livreurAmount = round($fraisLivraison * $livreurPctLivraison, 2);
        $commission = round((float) $paiement->montant * $commissionPct, 2);
        $pharmacieAmount = max((float) $paiement->montant - $livreurAmount - $commission, 0);

        $statutCantonnement = $commande->statut === 'livree' ? 'libere' : 'bloque';
        $dates = [
            'date_cantonnement' => now(),
            'date_liberation' => $statutCantonnement === 'libere' ? now() : null,
        ];

        $paiement->update(array_merge([
            'montant_pharmacie' => $pharmacieAmount,
            'montant_livreur' => $livreurAmount,
            'commission_plateforme' => $commission,
            'statut_cantonnement' => $statutCantonnement,
        ], $dates));
    }
}
