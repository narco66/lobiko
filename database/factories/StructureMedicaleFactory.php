<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StructureMedicale>
 */
class StructureMedicaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom_structure' => fake()->company() . ' Santé',
            'type_structure' => fake()->randomElement(['cabinet', 'clinique', 'hopital', 'pharmacie']),
            'adresse_rue' => fake()->streetAddress(),
            'adresse_quartier' => fake()->randomElement(['Glass', 'Louis', 'Oloumi']),
            'adresse_ville' => fake()->randomElement(['Libreville', 'Port-Gentil']),
            'adresse_pays' => 'Gabon',
            'telephone_principal' => fake()->numerify('+2410########'),
            'email' => fake()->companyEmail(),
            'horaires_ouverture' => [
                'monday' => '08:00-17:00',
                'tuesday' => '08:00-17:00',
                'wednesday' => '08:00-17:00',
                'thursday' => '08:00-17:00',
                'friday' => '08:00-17:00',
                'saturday' => '09:00-13:00',
                'sunday' => 'closed',
            ],
            'urgences_24h' => false,
            'tiers_payant' => true,
            'statut' => 'actif',
            'verified' => true,
            'verified_at' => now(),
        ];
    }
}
