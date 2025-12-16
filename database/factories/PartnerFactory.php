<?php

namespace Database\Factories;

use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;

class PartnerFactory extends Factory
{
    protected $model = Partner::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'logo' => 'logo.png',
            'website' => $this->faker->url(),
            'description' => $this->faker->sentence(),
            'type' => $this->faker->randomElement(['payment', 'insurance', 'medical', 'logistics', 'technology', 'other']),
            'order' => $this->faker->numberBetween(0, 10),
            'is_active' => true,
            'is_featured' => $this->faker->boolean(20),
        ];
    }
}
