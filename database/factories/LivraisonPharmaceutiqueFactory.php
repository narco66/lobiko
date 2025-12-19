<?php

namespace Database\Factories;

use App\Models\LivraisonPharmaceutique;
use App\Models\CommandePharmaceutique;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class LivraisonPharmaceutiqueFactory extends Factory
{
    protected $model = LivraisonPharmaceutique::class;

    public function definition(): array
    {
        $commande = CommandePharmaceutique::factory()->create();
        return [
            'commande_pharmaceutique_id' => $commande->id,
            'livreur_id' => User::factory()->create()->id,
            'numero_livraison' => 'LIV-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6)),
            'statut' => 'planifiee',
            'date_depart' => null,
            'date_arrivee_prevue' => now()->addHours(2),
            'date_livraison' => null,
            'nom_receptionnaire' => null,
            'telephone_receptionnaire' => null,
            'tracking_gps' => [],
            'distance_parcourue_km' => null,
        ];
    }

    public function enCours(): self
    {
        return $this->state(fn () => [
            'statut' => 'en_cours',
            'date_depart' => now(),
        ]);
    }

    public function livree(): self
    {
        return $this->state(fn () => [
            'statut' => 'livree',
            'date_depart' => now()->subHour(),
            'date_livraison' => now(),
            'nom_receptionnaire' => $this->faker->name,
            'telephone_receptionnaire' => $this->faker->phoneNumber,
        ]);
    }
}
