<?php

namespace Database\Factories;

use App\Models\DossierMedical;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DossierMedicalFactory extends Factory
{
    protected $model = DossierMedical::class;

    public function definition(): array
    {
        return [
            'patient_id' => User::factory(),
            'numero_dossier' => 'DM-' . Str::upper(Str::random(8)),
            'derniere_consultation' => now(),
            'nombre_consultations' => 0,
            'allergies' => ['pollen'],
            'antecedents' => ['hypertension'],
            'traitements_en_cours' => ['amlodipine'],
        ];
    }
}
