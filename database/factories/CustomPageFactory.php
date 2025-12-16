<?php

namespace Database\Factories;

use App\Models\CustomPage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CustomPageFactory extends Factory
{
    protected $model = CustomPage::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(3);

        return [
            'title' => $title,
            'slug' => Str::slug($title . '-' . $this->faker->unique()->randomNumber()),
            'content' => $this->faker->paragraphs(2, true),
            'template' => 'default',
            'meta_data' => ['title' => $title],
            'is_published' => true,
            'in_menu' => $this->faker->boolean(30),
            'menu_order' => $this->faker->numberBetween(0, 10),
        ];
    }
}
