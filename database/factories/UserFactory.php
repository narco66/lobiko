<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => fake()->lastName(),
            'prenom' => fake()->firstName(),
            'date_naissance' => fake()->dateTimeBetween('-60 years', '-18 years'),
            'sexe' => fake()->randomElement(['M', 'F']),
            'telephone' => fake()->unique()->numerify('07########'),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'adresse_rue' => fake()->streetName(),
            'adresse_quartier' => fake()->randomElement(['Glass', 'Louis', 'Oloumi', 'Nkembo']),
            'adresse_ville' => fake()->randomElement(['Libreville', 'Port-Gentil', 'Franceville']),
            'adresse_pays' => 'Gabon',
            'statut_compte' => 'actif',
            'langue_preferee' => 'fr',
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function patient(): static
    {
        return $this->state(fn () => [
            'statut_compte' => 'actif',
        ])->afterCreating(function ($user) {
            if (method_exists($user, 'assignRole')) {
                $user->assignRole('patient');
            }
        });
    }

    public function medecin(): static
    {
        return $this->state(fn () => [
            'specialite' => fake()->randomElement(['Médecine Générale', 'Cardiologie', 'Pédiatrie']),
            'numero_ordre' => 'OMG-' . fake()->year() . '-' . fake()->numberBetween(1000, 9999),
            'certification_verified' => true,
        ])->afterCreating(function ($user) {
            if (method_exists($user, 'assignRole')) {
                $user->assignRole('medecin');
            }
        });
    }

    public function pharmacien(): static
    {
        return $this->state(fn () => [
            'specialite' => 'Pharmacie',
            'numero_ordre' => 'OPG-' . fake()->year() . '-' . fake()->numberBetween(1000, 9999),
            'certification_verified' => true,
        ])->afterCreating(function ($user) {
            if (method_exists($user, 'assignRole')) {
                $user->assignRole('pharmacien');
            }
        });
    }
}
