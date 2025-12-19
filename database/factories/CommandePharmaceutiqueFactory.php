<?php

namespace Database\Factories;

use App\Models\CommandePharmaceutique;
use App\Models\User;
use App\Models\Pharmacie;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CommandePharmaceutiqueFactory extends Factory
{
    protected $model = CommandePharmaceutique::class;

    public function definition(): array
    {
        $patient = User::factory()->create();
        $pharmacie = Pharmacie::factory()->create();
        return [
            'numero_commande' => 'CMD-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6)),
            'patient_id' => $patient->id,
            'pharmacie_id' => $pharmacie->id,
            'ordonnance_id' => null,
            'montant_total' => 5000,
            'montant_assurance' => 0,
            'montant_patient' => 5000,
            'mode_retrait' => 'sur_place',
            'adresse_livraison' => null,
            'latitude_livraison' => null,
            'longitude_livraison' => null,
            'frais_livraison' => 0,
            'commission_taux' => 10,
            'commission_montant' => 500,
            'montant_net_pharmacie' => 4500,
            'statut_commission' => 'en_attente',
            'statut' => 'en_attente',
            'statut_paiement' => 'en_attente',
            'date_commande' => now(),
            'code_retrait' => Str::upper(Str::random(8)),
            'urgent' => false,
        ];
    }

    public function livraison(): self
    {
        return $this->state(fn () => [
            'mode_retrait' => 'livraison',
            'statut' => 'prete',
            'frais_livraison' => 1500,
            'adresse_livraison' => 'Adresse livraison test',
            'latitude_livraison' => 0.1,
            'longitude_livraison' => 0.1,
        ]);
    }
}
