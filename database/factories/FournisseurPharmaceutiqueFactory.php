<?php

namespace Database\Factories;

use App\Models\FournisseurPharmaceutique;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FournisseurPharmaceutiqueFactory extends Factory
{
    protected $model = FournisseurPharmaceutique::class;

    public function definition(): array
    {
        return [
            'nom_fournisseur' => $this->faker->company,
            'numero_licence' => 'FOU-' . Str::upper(Str::random(6)),
            'telephone' => $this->faker->phoneNumber,
            'email' => $this->faker->companyEmail,
            'adresse' => $this->faker->address,
            'personne_contact' => $this->faker->name,
            'telephone_contact' => $this->faker->phoneNumber,
            'categories_produits' => ['Médicaments', 'Consommables'],
            'delai_livraison_jours' => 3,
            'montant_minimum_commande' => $this->faker->randomFloat(2, 10000, 50000),
            'statut' => 'actif',
        ];
    }
}
