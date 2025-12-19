<?php

namespace App\Services;

use App\Models\Consultation;
use App\Models\Facture;
use Illuminate\Support\Facades\DB;

class FacturationService
{
    /**
     * Génère une facture simple pour une consultation (placeholder minimal).
     */
    public function genererFacturePourConsultation(Consultation $consultation, array $lignes = []): Facture
    {
        return DB::transaction(function () use ($consultation, $lignes) {
            $facture = Facture::create([
                'consultation_id' => $consultation->id,
                'patient_id' => $consultation->patient_id,
                'structure_id' => $consultation->structure_id,
                'praticien_id' => $consultation->professionnel_id,
                'statut' => 'brouillon',
                'montant_ht' => 0,
                'montant_tva' => 0,
                'montant_ttc' => 0,
                'montant_final' => 0,
            ]);

            // Ajout éventuel de lignes (optionnel)
            foreach ($lignes as $ligne) {
                $facture->lignes()->create($ligne);
            }

            return $facture;
        });
    }
}
