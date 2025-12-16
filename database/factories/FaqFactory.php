<?php

namespace Database\Factories;

use App\Models\Faq;
use Illuminate\Database\Eloquent\Factories\Factory;

class FaqFactory extends Factory
{
    protected $model = Faq::class;

    public function definition(): array
    {
        return [
            'category' => $this->faker->randomElement(['Général', 'Paiement', 'Consultation']),
            'question' => $this->faker->sentence(),
            'answer' => $this->faker->paragraph(),
            'order' => $this->faker->numberBetween(0, 20),
            'is_published' => true,
            'helpful_count' => $this->faker->numberBetween(0, 50),
            'not_helpful_count' => $this->faker->numberBetween(0, 10),
        ];
    }
}
