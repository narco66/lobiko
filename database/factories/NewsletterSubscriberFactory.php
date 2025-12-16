<?php

namespace Database\Factories;

use App\Models\NewsletterSubscriber;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NewsletterSubscriberFactory extends Factory
{
    protected $model = NewsletterSubscriber::class;

    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail(),
            'name' => $this->faker->name(),
            'is_subscribed' => true,
            'token' => Str::uuid()->toString(),
            'confirmed_at' => now(),
            'unsubscribed_at' => null,
            'ip_address' => $this->faker->ipv4(),
        ];
    }
}
