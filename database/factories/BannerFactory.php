<?php

namespace Database\Factories;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Factories\Factory;

class BannerFactory extends Factory
{
    protected $model = Banner::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->sentence(),
            'image' => 'banner.jpg',
            'link' => $this->faker->url(),
            'button_text' => 'Découvrir',
            'position' => $this->faker->randomElement(['home_top', 'home_middle', 'home_bottom', 'sidebar', 'popup']),
            'start_date' => now()->subDays(5),
            'end_date' => now()->addDays(20),
            'is_active' => true,
            'click_count' => $this->faker->numberBetween(0, 100),
            'view_count' => $this->faker->numberBetween(100, 1000),
        ];
    }
}
