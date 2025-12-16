<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Consultation>
 */
class ConsultationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'numero_consultation' => 'CONS-' . now()->format('Ymd') . '-' . fake()->unique()->numerify('####'),
            'patient_id' => \App\Models\User::factory(),
            'professionnel_id' => \App\Models\User::factory()->state(['specialite' => 'Médecine Générale']),
            'structure_id' => \App\Models\StructureMedicale::factory(),
            'date_consultation' => now(),
            'heure_debut' => now()->format('H:i:s'),
            'type' => 'generale',
            'modalite' => fake()->randomElement(['presentiel', 'teleconsultation']),
            'motif_consultation' => fake()->sentence(6),
            'diagnostic_principal' => fake()->words(3, true),
            'conduite_a_tenir' => 'Repos et hydratation',
        ];
    }
}
