<?php

namespace Database\Factories;

use App\Models\ConventionRule;
use App\Models\Convention;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ConventionRuleFactory extends Factory
{
    protected $model = ConventionRule::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid()->toString(),
            'convention_id' => Convention::factory(),
            'categorie' => 'MEDICAMENT',
            'taux_prise_en_charge' => 80,
            'plafond_par_acte' => 5000,
            'ticket_moderateur' => 1000,
            'franchise' => 0,
            'prior_authorization_required' => false,
            'is_active' => true,
        ];
    }
}
