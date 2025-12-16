<?php

namespace Database\Factories;

use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestimonialFactory extends Factory
{
    protected $model = Testimonial::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'content' => $this->faker->sentences(2, true),
            'rating' => $this->faker->numberBetween(3, 5),
            'is_published' => true,
            'is_featured' => $this->faker->boolean(20),
            'published_at' => now()->subDays($this->faker->numberBetween(0, 30)),
        ];
    }
}
