<?php

namespace Database\Factories;

use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DoctorFactory extends Factory
{
    protected $model = Doctor::class;

    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'matricule' => 'DOC-' . strtoupper(Str::random(6)),
            'nom' => $this->faker->lastName,
            'prenom' => $this->faker->firstName,
            'telephone' => $this->faker->phoneNumber,
            'email' => $this->faker->safeEmail,
            'statut' => 'actif',
        ];
    }
}
