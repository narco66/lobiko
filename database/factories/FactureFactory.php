<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Facture>
 */
class FactureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $montant = $this->faker->randomFloat(2, 100, 2000);
        $montantTva = 0;
        $montantFinal = $montant + $montantTva;

        return [
            'numero_facture' => 'FAC-' . now()->format('Ym') . '-' . $this->faker->unique()->numerify('#####'),
            'patient_id' => User::factory(),
            'praticien_id' => User::factory()->medecin(),
            'structure_id' => null,
            'type' => 'consultation',
            'nature' => 'normale',
            'montant_ht' => $montant,
            'montant_tva' => $montantTva,
            'montant_ttc' => $montantFinal,
            'montant_remise' => 0,
            'montant_majoration' => 0,
            'montant_final' => $montantFinal,
            'part_patient' => $montantFinal,
            'part_assurance' => 0,
            'part_subvention' => 0,
            'repartition_payeurs' => null,
            'pec_id' => null,
            'tiers_payant' => false,
            'montant_pec' => 0,
            'reste_a_charge' => $montantFinal,
            'date_facture' => now(),
            'date_echeance' => now()->addDays(30),
            'delai_paiement' => 30,
            'statut_paiement' => 'en_attente',
            'montant_paye' => 0,
            'montant_restant' => $montantFinal,
            'nombre_relances' => 0,
            'nombre_paiements' => 0,
            'originale_remise' => false,
            'comptabilisee' => false,
            'notes_internes' => null,
            'mentions_legales' => null,
        ];
    }
}
