<?php

namespace Database\Factories;

use App\Models\JobOffer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class JobOfferFactory extends Factory
{
    protected $model = JobOffer::class;

    public function definition(): array
    {
        $title = $this->faker->jobTitle();

        return [
            'title' => $title,
            'slug' => Str::slug($title . '-' . $this->faker->unique()->randomNumber()),
            'department' => $this->faker->randomElement(['Tech', 'Ops', 'Medical']),
            'location' => $this->faker->city(),
            'type' => $this->faker->randomElement(['full_time', 'part_time', 'contract', 'internship', 'freelance']),
            'level' => $this->faker->randomElement(['junior', 'mid', 'senior', 'lead']),
            'description' => $this->faker->paragraph(),
            'requirements' => $this->faker->paragraph(),
            'benefits' => $this->faker->sentence(),
            'salary_min' => $this->faker->randomFloat(2, 300, 800),
            'salary_max' => $this->faker->randomFloat(2, 801, 1200),
            'salary_currency' => 'XAF',
            'is_remote' => $this->faker->boolean(30),
            'is_active' => true,
            'expires_at' => now()->addDays($this->faker->numberBetween(10, 60)),
            'applications_count' => 0,
        ];
    }
}
