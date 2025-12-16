<?php

namespace Database\Factories;

use App\Models\ArticleCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ArticleCategoryFactory extends Factory
{
    protected $model = ArticleCategory::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name . '-' . $this->faker->unique()->randomNumber()),
            'description' => $this->faker->sentence(),
            'icon' => 'fa-' . $this->faker->word(),
            'order' => $this->faker->numberBetween(0, 10),
            'is_active' => true,
        ];
    }
}
