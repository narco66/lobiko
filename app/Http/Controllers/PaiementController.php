<?php

namespace App\Http\Controllers;

use App\Events\PaymentConfirmed;
use App\Http\Requests\PaiementRequest;
use App\Http\Resources\PaiementResource;
use App\Models\Facture;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaiementController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(PaiementRequest $request)
    {
        $validated = $request->validated();

        $facture = Facture::findOrFail($validated['facture_id']);
        $payeurId = $validated['payeur_id'] ?? $facture->patient_id;

        $payload = array_merge($validated, [
            'payeur_id' => $payeurId,
            'numero_paiement' => Paiement::generateNumero(),
            'idempotence_key' => $validated['idempotence_key'] ?? $request->header('Idempotency-Key') ?? (string) Str::uuid(),
            'reference_transaction' => $validated['reference_transaction'] ?? strtoupper(Str::random(14)),
            'montant_devise_locale' => $validated['montant_devise_locale'] ?? round($validated['montant'] * ($validated['taux_change'] ?? 1), 2),
            'montant_net' => $validated['montant_net'] ?? max($validated['montant'] - ($validated['frais_transaction'] ?? 0), 0),
            'date_initiation' => now(),
            'statut' => 'initie',
        ]);

        $paiement = Paiement::create($payload);

        // Mettre à jour la facture
        $facture->updateRemainingAmount();

        return (new PaiementResource($paiement))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Paiement $paiement): PaiementResource
    {
        return new PaiementResource($paiement);
    }

    /**
     * Confirmer un paiement (hook passerelle ou backoffice)
     */
    public function confirm(Request $request, Paiement $paiement): PaiementResource
    {
        if ($paiement->statut === 'confirme') {
            return new PaiementResource($paiement);
        }

        $paiement->update([
            'statut' => 'confirme',
            'date_confirmation' => now(),
            'reference_passerelle' => $request->input('reference_passerelle', $paiement->reference_passerelle),
            'code_autorisation' => $request->input('code_autorisation', $paiement->code_autorisation),
            'reponse_passerelle' => $request->input('reponse_passerelle', $paiement->reponse_passerelle),
        ]);

        $paiement->facture?->updateRemainingAmount();

        PaymentConfirmed::dispatch($paiement);

        return new PaiementResource($paiement->fresh());
    }
}
