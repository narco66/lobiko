<?php

namespace Database\Factories;

use App\Models\Facture;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FactureFactory extends Factory
{
    protected $model = Facture::class;

    public function definition(): array
    {
        $patient = User::factory()->create();
        $pro = User::factory()->medecin()->create();

        return [
            'numero_facture' => 'FAC-' . Str::upper(Str::random(8)),
            'patient_id' => $patient->id,
            'praticien_id' => $pro->id,
            'type' => 'consultation',
            'nature' => 'normale',
            'montant_ht' => 1000,
            'montant_tva' => 0,
            'montant_ttc' => 1000,
            'montant_remise' => 0,
            'montant_majoration' => 0,
            'montant_final' => 1000,
            'part_patient' => 1000,
            'part_assurance' => 0,
            'part_subvention' => 0,
            'reste_a_charge' => 1000,
            'date_facture' => now()->toDateString(),
            'date_echeance' => now()->addDays(30)->toDateString(),
            'montant_paye' => 0,
            'montant_restant' => 1000,
            'statut_paiement' => 'en_attente',
            'delai_paiement' => 30,
            'nombre_paiements' => 0,
            'nombre_relances' => 0,
        ];
    }
}
