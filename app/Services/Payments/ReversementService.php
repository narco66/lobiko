<?php

namespace App\Services\Payments;

use App\Models\CommandePharmaceutique;
use App\Models\Paiement;
use App\Models\Reversement;
use Illuminate\Support\Facades\DB;

class ReversementService
{
    /**
     * Génère/agrège les reversements pour pharmacies et livreurs à partir des paiements libérés.
     * @return array{pharmacies:int, livreurs:int}
     */
    public function traiterPaiementsLiberes(): array
    {
        $pharmacieCount = 0;
        $livreurCount = 0;

        $paiements = Paiement::where('statut_cantonnement', 'libere')
            ->where('reversement_genere', false)
            ->with('commande.pharmacie')
            ->get();

        foreach ($paiements as $paiement) {
            $commande = $paiement->commande ?? $this->getCommandeViaReference($paiement);
            if (!$commande) {
                continue;
            }

            DB::transaction(function () use ($paiement, $commande, &$pharmacieCount, &$livreurCount) {
                if ($paiement->montant_pharmacie > 0) {
                    $reversement = $this->reversementPourBeneficiaire(
                        $commande->pharmacie_id,
                        'structure'
                    );
                    $this->incrementReversement($reversement, $paiement->montant_pharmacie, $paiement->commission_plateforme);
                    $paiement->reversement_pharmacie_id = $reversement->id;
                    $pharmacieCount++;
                }

                if ($paiement->montant_livreur > 0 && $commande->livraison?->livreur_id) {
                    $reversementLivreur = $this->reversementPourBeneficiaire(
                        $commande->livraison->livreur_id,
                        'praticien' // assimilé bénéficiaire, faute de type livreur dans le schéma
                    );
                    $this->incrementReversement($reversementLivreur, $paiement->montant_livreur, 0);
                    $paiement->reversement_livreur_id = $reversementLivreur->id;
                    $livreurCount++;
                }

                $paiement->reversement_genere = true;
                $paiement->payout_tagged_at = now();
                $paiement->save();
            });
        }

        return ['pharmacies' => $pharmacieCount, 'livreurs' => $livreurCount];
    }

    protected function reversementPourBeneficiaire(string $beneficiaireId, string $type): Reversement
    {
        $moisAnnee = now()->format('Y-m');

        return Reversement::firstOrCreate(
            [
                'beneficiaire_id' => $beneficiaireId,
                'type_beneficiaire' => $type,
                'mois_annee' => $moisAnnee,
            ],
            [
                'numero_reversement' => Reversement::generateNumero(),
                'periode_debut' => now()->startOfMonth(),
                'periode_fin' => now()->endOfMonth(),
                'montant_brut' => 0,
                'commission_plateforme' => 0,
                'taux_commission' => 0,
                'montant_net' => 0,
                'date_calcul' => now(),
                'date_paiement_prevu' => now()->endOfMonth()->addDays(5),
                'statut' => 'calcule',
                'mode_paiement' => 'virement',
            ]
        );
    }

    protected function incrementReversement(Reversement $reversement, float $montant, float $commission): void
    {
        $reversement->update([
            'montant_brut' => $reversement->montant_brut + $montant,
            'commission_plateforme' => $reversement->commission_plateforme + $commission,
            'montant_net' => $reversement->montant_net + ($montant - $commission),
        ]);
    }

    protected function getCommandeViaReference(Paiement $paiement): ?CommandePharmaceutique
    {
        if ($paiement->type_reference === 'commande_pharmaceutique' && $paiement->reference_id) {
            return CommandePharmaceutique::find($paiement->reference_id);
        }
        return null;
    }
}
