<?php

namespace Database\Factories;

use App\Models\JobApplication;
use App\Models\JobOffer;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobApplicationFactory extends Factory
{
    protected $model = JobApplication::class;

    public function definition(): array
    {
        return [
            'job_offer_id' => JobOffer::factory(),
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'cv_file' => 'cv.pdf',
            'cover_letter_file' => null,
            'message' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['new', 'reviewing', 'shortlisted', 'interviewed']),
            'notes' => null,
            'reviewed_at' => null,
            'reviewed_by' => null,
        ];
    }
}
