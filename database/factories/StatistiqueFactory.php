<?php

namespace Database\Factories;

use App\Models\Statistique;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class StatistiqueFactory extends Factory
{
    protected $model = Statistique::class;

    public function definition(): array
    {
        $key = Str::slug($this->faker->unique()->words(2, true));

        return [
            'key' => $key,
            'label' => ucfirst(str_replace('-', ' ', $key)),
            'value' => $this->faker->numberBetween(10, 1000),
            'unit' => $this->faker->randomElement(['', 'patients', 'consultations']),
            'icon' => 'fa-' . $this->faker->word(),
            'is_visible' => true,
            'order' => $this->faker->numberBetween(0, 10),
        ];
    }
}
