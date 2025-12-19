<?php

namespace Database\Factories;

use App\Models\Facture;
use App\Models\Paiement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PaiementFactory extends Factory
{
    protected $model = Paiement::class;

    public function definition(): array
    {
        return [
            'numero_paiement' => Paiement::generateNumero(),
            'facture_id' => Facture::factory(),
            'payeur_id' => null,
            'mode_paiement' => 'especes',
            'montant' => 1000,
            'devise' => 'XAF',
            'statut' => 'initie',
            'idempotence_key' => (string) Str::uuid(),
            'reference_transaction' => Str::upper(Str::random(10)),
            'date_initiation' => now(),
        ];
    }
}
