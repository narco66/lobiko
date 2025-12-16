<?php

namespace Database\Factories;

use App\Models\TeamMember;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamMemberFactory extends Factory
{
    protected $model = TeamMember::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'role' => $this->faker->jobTitle(),
            'photo' => 'team.jpg',
            'bio' => $this->faker->paragraph(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'social_links' => [
                'linkedin' => $this->faker->url(),
                'twitter' => $this->faker->url(),
            ],
            'order' => $this->faker->numberBetween(0, 10),
            'is_active' => true,
        ];
    }
}
