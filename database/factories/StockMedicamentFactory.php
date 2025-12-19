<?php

namespace Database\Factories;

use App\Models\StockMedicament;
use App\Models\Pharmacie;
use App\Models\ProduitPharmaceutique;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class StockMedicamentFactory extends Factory
{
    protected $model = StockMedicament::class;

    public function definition(): array
    {
        $pharmacie = Pharmacie::factory()->create();
        $produit = ProduitPharmaceutique::factory()->create();

        $quantite = $this->faker->numberBetween(5, 200);

        return [
            'pharmacie_id' => $pharmacie->id,
            'produit_pharmaceutique_id' => $produit->id,
            'quantite_disponible' => $quantite,
            'quantite_minimum' => 5,
            'quantite_maximum' => 500,
            'prix_vente' => $this->faker->randomFloat(2, 500, 10000),
            'prix_achat' => $this->faker->randomFloat(2, 300, 8000),
            'numero_lot' => 'LOT-' . Str::upper(Str::random(6)),
            'date_expiration' => now()->addMonths(6),
            'emplacement_rayon' => 'A' . $this->faker->numberBetween(1, 20),
            'prescription_requise' => $this->faker->boolean(40),
            'disponible_vente' => true,
            'statut_stock' => 'disponible',
        ];
    }

    public function rupture(): self
    {
        return $this->state(fn () => ['quantite_disponible' => 0, 'statut_stock' => 'rupture']);
    }

    public function expire(): self
    {
        return $this->state(fn () => [
            'date_expiration' => now()->subMonth(),
            'statut_stock' => 'expire',
            'disponible_vente' => false,
        ]);
    }

    public function faible(): self
    {
        return $this->state(fn () => ['quantite_disponible' => 2, 'statut_stock' => 'faible']);
    }
}
