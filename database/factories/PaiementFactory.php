<?php

namespace Database\Factories;

use App\Models\Facture;
use App\Models\Paiement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Paiement>
 */
class PaiementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $montant = $this->faker->randomFloat(2, 10, 500);
        $fees = $this->faker->randomFloat(2, 0, 5);

        return [
            'facture_id' => Facture::factory(),
            'payeur_id' => User::factory(),
            'type_payeur' => 'patient',
            'mode_paiement' => $this->faker->randomElement([
                'especes',
                'carte_bancaire',
                'virement',
                'cheque',
                'mobile_money_airtel',
                'mobile_money_mtn',
                'mobile_money_orange',
                'mobile_money_moov',
                'paypal',
                'voucher'
            ]),
            'montant' => $montant,
            'devise' => 'XAF',
            'taux_change' => 1,
            'montant_devise_locale' => $montant,
            'frais_transaction' => $fees,
            'montant_net' => max($montant - $fees, 0),
            'numero_paiement' => Paiement::generateNumero(),
            'reference_transaction' => strtoupper(Str::random(14)),
            'idempotence_key' => (string) Str::uuid(),
            'statut' => 'initie',
            'date_initiation' => now(),
        ];
    }
}
