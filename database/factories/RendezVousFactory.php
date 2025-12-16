<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RendezVous>
 */
class RendezVousFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'numero_rdv' => 'RDV-' . now()->format('Ymd') . '-' . fake()->unique()->numerify('####'),
            'patient_id' => \App\Models\User::factory(),
            'professionnel_id' => \App\Models\User::factory()->state(['specialite' => 'Médecine Générale']),
            'structure_id' => \App\Models\StructureMedicale::factory(),
            'date_heure' => fake()->dateTimeBetween('+1 day', '+10 days'),
            'duree_prevue' => 30,
            'type' => 'consultation',
            'modalite' => fake()->randomElement(['presentiel', 'teleconsultation']),
            'specialite' => 'Médecine Générale',
            'motif' => fake()->sentence(6),
            'statut' => fake()->randomElement(['en_attente', 'confirme']),
        ];
    }
}
