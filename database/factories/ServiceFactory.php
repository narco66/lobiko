<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name . '-' . $this->faker->unique()->randomNumber()),
            'description' => $this->faker->sentence(),
            'full_description' => $this->faker->paragraph(),
            'icon' => 'fa-' . $this->faker->word(),
            'image' => null,
            'order' => $this->faker->numberBetween(0, 10),
            'is_active' => true,
            'is_featured' => $this->faker->boolean(30),
            'features' => [$this->faker->sentence(), $this->faker->sentence()],
            'base_price' => $this->faker->randomFloat(2, 5, 200),
            'price_unit' => 'XAF',
        ];
    }
}
