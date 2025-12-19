<?php

namespace Database\Factories;

use App\Models\RendezVous;
use App\Models\StructureMedicale;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RendezVousFactory extends Factory
{
    protected $model = RendezVous::class;

    public function definition(): array
    {
        $start = now()->addDays(1)->setTime(10, 0);

        return [
            'id' => Str::uuid()->toString(),
            'numero_rdv' => 'RDV-' . now()->format('Ymd') . '-' . Str::upper(Str::random(4)),
            'patient_id' => User::factory(),
            'professionnel_id' => User::factory()->medecin(),
            'structure_id' => StructureMedicale::factory(),
            'date_heure' => $start,
            'duree_prevue' => 30,
            'date_heure_fin' => $start->copy()->addMinutes(30),
            'type' => 'consultation',
            'modalite' => 'presentiel',
            'specialite' => 'MǸdecine GǸnǸrale',
            'motif' => 'Consultation de test',
            'statut' => 'en_attente',
        ];
    }

    public function confirme(): self
    {
        return $this->state(fn () => ['statut' => 'confirme']);
    }

    public function annule(): self
    {
        return $this->state(fn () => ['statut' => 'annule']);
    }
}

