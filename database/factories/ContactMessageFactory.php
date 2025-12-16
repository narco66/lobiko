<?php

namespace Database\Factories;

use App\Models\ContactMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactMessageFactory extends Factory
{
    protected $model = ContactMessage::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'subject' => $this->faker->sentence(),
            'message' => $this->faker->paragraph(),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'status' => $this->faker->randomElement(['new', 'read', 'replied']),
            'admin_notes' => null,
            'replied_at' => null,
            'replied_by' => null,
        ];
    }
}
