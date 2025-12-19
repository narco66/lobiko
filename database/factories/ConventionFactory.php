<?php

namespace Database\Factories;

use App\Models\Convention;
use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ConventionFactory extends Factory
{
    protected $model = Convention::class;

    public function definition(): array
    {
        $assureur = Partner::factory()->assureur()->create();
        $prestataire = Partner::factory()->pharmacie()->create();

        return [
            'id' => Str::uuid()->toString(),
            'assureur_partner_id' => $assureur->id,
            'prestataire_partner_id' => $prestataire->id,
            'code_convention' => 'CONV-' . now()->format('Ymd') . '-' . Str::upper(Str::random(4)),
            'libelle' => 'Convention ' . $this->faker->word(),
            'objet' => 'Convention de test',
            'date_debut' => now()->toDateString(),
            'date_fin' => now()->addYear()->toDateString(),
            'statut' => 'ACTIVE',
            'mode_facturation' => 'POST_PAY',
            'delai_remboursement_jours' => 30,
        ];
    }
}
