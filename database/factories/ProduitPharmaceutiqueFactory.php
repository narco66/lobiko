<?php

namespace Database\Factories;

use App\Models\ProduitPharmaceutique;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProduitPharmaceutiqueFactory extends Factory
{
    protected $model = ProduitPharmaceutique::class;

    public function definition(): array
    {
        $code = Str::upper(Str::random(8));
        return [
            'id' => Str::uuid()->toString(),
            'code_produit' => $code,
            'dci' => $this->faker->word,
            'nom_commercial' => ucfirst($this->faker->word) . ' ' . $this->faker->randomElement(['500mg', '1g', '250mg']),
            'laboratoire' => $this->faker->company,
            'forme' => $this->faker->randomElement(['comprimé', 'gélule', 'sirop']),
            'dosage' => $this->faker->randomElement(['500mg', '1g', '250mg/5ml']),
            'conditionnement' => 'Boîte de ' . $this->faker->randomElement([10, 15, 30]),
            'voie_administration' => $this->faker->randomElement(['orale', 'injectable', 'topique']),
            'classe_therapeutique' => $this->faker->randomElement(['Antalgique', 'Antibiotique', 'Antihypertenseur']),
            'famille' => $this->faker->randomElement(['AINS', 'Pénicilline', 'Macrolide']),
            'generique' => $this->faker->boolean(50),
            'princeps' => null,
            'prix_unitaire' => $this->faker->randomFloat(2, 200, 5000),
            'prix_boite' => $this->faker->randomFloat(2, 500, 8000),
            'stock_minimum' => 10,
            'stock_alerte' => 20,
            'prescription_obligatoire' => $this->faker->boolean(60),
            'stupefiant' => false,
            'liste_i' => false,
            'liste_ii' => false,
            'duree_traitement_max' => 30,
            'remboursable' => true,
            'taux_remboursement' => 65,
            'code_cip' => 'CIP-' . Str::upper(Str::random(6)),
            'code_ucd' => 'UCD-' . Str::upper(Str::random(6)),
            'disponible' => true,
            'rupture_stock' => false,
        ];
    }
}
