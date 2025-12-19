<?php

namespace Database\Factories;

use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PartnerFactory extends Factory
{
    protected $model = Partner::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'logo' => null,
            'website' => $this->faker->url(),
            'description' => $this->faker->sentence(),
            'type' => 'insurance',
            'partner_type' => 'ASSUREUR',
            'statut' => 'actif',
            'commission_mode' => 'percent',
            'commission_value' => 10,
            'contact_email' => $this->faker->safeEmail(),
            'contact_phone' => $this->faker->phoneNumber(),
            'adresse_ville' => 'Libreville',
            'adresse_pays' => 'Gabon',
            'numero_legal' => 'RCCM-' . Str::random(6),
            'order' => 0,
            'is_active' => true,
            'is_featured' => false,
        ];
    }

    public function assureur(): self
    {
        return $this->state(fn () => ['partner_type' => 'ASSUREUR']);
    }

    public function pharmacie(): self
    {
        return $this->state(fn () => ['partner_type' => 'PHARMACIE']);
    }
}
