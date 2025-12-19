<?php

namespace Database\Factories;

use App\Models\AlerteStock;
use App\Models\Pharmacie;
use App\Models\StockMedicament;
use Illuminate\Database\Eloquent\Factories\Factory;

class AlerteStockFactory extends Factory
{
    protected $model = AlerteStock::class;

    public function definition(): array
    {
        $stock = StockMedicament::factory()->faible()->create();
        return [
            'pharmacie_id' => $stock->pharmacie_id,
            'stock_medicament_id' => $stock->id,
            'type_alerte' => 'stock_faible',
            'message' => 'Stock faible pour ' . $stock->produit_pharmaceutique_id,
            'vue' => false,
            'traitee' => false,
        ];
    }
}
