<?php

namespace Database\Factories;

use App\Models\Reversement;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ReversementFactory extends Factory
{
    protected $model = Reversement::class;

    public function definition(): array
    {
        $mois = now()->format('Y-m');
        $brut = $this->faker->randomFloat(2, 10000, 50000);
        $commission = round($brut * 0.05, 2);
        return [
            'numero_reversement' => Reversement::generateNumero(),
            'beneficiaire_id' => Str::uuid()->toString(),
            'type_beneficiaire' => 'structure',
            'periode_debut' => now()->startOfMonth(),
            'periode_fin' => now()->endOfMonth(),
            'mois_annee' => $mois,
            'montant_brut' => $brut,
            'commission_plateforme' => $commission,
            'taux_commission' => 5,
            'retenues_fiscales' => 0,
            'autres_retenues' => 0,
            'montant_net' => $brut - $commission,
            'nombre_consultations' => 0,
            'nombre_actes' => 0,
            'mode_paiement' => 'virement',
            'compte_beneficiaire' => 'FR' . $this->faker->iban(null, 'FR'),
            'reference_paiement' => null,
            'statut' => 'calcule',
            'date_calcul' => now(),
            'date_validation' => null,
            'date_paiement_prevu' => now()->endOfMonth()->addDays(5),
            'date_paiement_effectif' => null,
        ];
    }
}
