<?php

namespace Database\Factories;

use App\Models\Pharmacie;
use App\Models\StructureMedicale;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PharmacieFactory extends Factory
{
    protected $model = Pharmacie::class;

    public function definition(): array
    {
        $structure = StructureMedicale::factory()->create([
            'type_structure' => 'pharmacie',
        ]);

        return [
            'structure_medicale_id' => $structure->id,
            'numero_licence' => 'PH-' . Str::upper(Str::random(6)),
            'nom_pharmacie' => 'Pharmacie ' . $this->faker->company,
            'nom_responsable' => $this->faker->name,
            'telephone_pharmacie' => $this->faker->phoneNumber,
            'email_pharmacie' => $this->faker->safeEmail,
            'adresse_complete' => $this->faker->address,
            'latitude' => $this->faker->latitude(-4, 2),
            'longitude' => $this->faker->longitude(8, 15),
            'horaires_ouverture' => [
                'monday' => ['ouvert' => true, 'ouverture' => '08:00', 'fermeture' => '20:00'],
                'tuesday' => ['ouvert' => true, 'ouverture' => '08:00', 'fermeture' => '20:00'],
            ],
            'service_garde' => $this->faker->boolean(20),
            'livraison_disponible' => true,
            'rayon_livraison_km' => $this->faker->randomFloat(1, 5, 20),
            'frais_livraison_base' => $this->faker->randomFloat(2, 1000, 3000),
            'frais_livraison_par_km' => $this->faker->randomFloat(2, 100, 300),
            'paiement_mobile_money' => true,
            'paiement_carte' => $this->faker->boolean(50),
            'paiement_especes' => true,
            'statut' => 'active',
        ];
    }
}
