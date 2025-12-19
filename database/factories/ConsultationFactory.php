<?php

namespace Database\Factories;

use App\Models\Consultation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ConsultationFactory extends Factory
{
    protected $model = Consultation::class;

    public function definition(): array
    {
        return [
            'numero_consultation' => 'CONS-' . now()->format('Ymd') . '-' . $this->faker->unique()->numberBetween(1000, 9999),
            'patient_id' => User::factory(),
            'professionnel_id' => User::factory()->medecin(),
            'date_consultation' => now()->toDateString(),
            'type' => 'generale',
            'modalite' => 'presentiel',
            'motif_consultation' => 'Test motif',
            'diagnostic_principal' => 'Test diagnostic',
            'conduite_a_tenir' => 'Repos',
            'ordonnance_delivree' => false,
            'arret_travail' => false,
            'orientation_specialiste' => false,
            'hospitalisation' => false,
            'valide' => false,
        ];
    }
}
