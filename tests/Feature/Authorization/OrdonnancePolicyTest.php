<?php

namespace Tests\Feature\Authorization;

use App\Models\Ordonnance;
use App\Models\User;
use App\Policies\OrdonnancePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class OrdonnancePolicyTest extends TestCase
{
    use RefreshDatabase;

    private function makeOrdonnance(User $patient, User $prescripteur): Ordonnance
    {
        return Ordonnance::create([
            'patient_id' => $patient->id,
            'consultation_id' => (string) Str::uuid(),
            'prescripteur_id' => $prescripteur->id,
            'contenu' => ['medicaments' => [['nom' => 'Test', 'dose' => '1x/j']]],
            'statut' => 'validee',
        ]);
    }

    public function test_patient_peut_voir_ordonnance(): void
    {
        $patient = User::factory()->create();
        $prescripteur = User::factory()->create();
        $ordonnance = $this->makeOrdonnance($patient, $prescripteur);
        $policy = new OrdonnancePolicy();

        $this->assertTrue($policy->view($patient, $ordonnance));
    }

    public function test_autre_patient_ne_peut_pas_voir_ordonnance(): void
    {
        $patient = User::factory()->create();
        $other = User::factory()->create();
        $prescripteur = User::factory()->create();
        $ordonnance = $this->makeOrdonnance($patient, $prescripteur);
        $policy = new OrdonnancePolicy();

        $this->assertFalse($policy->view($other, $ordonnance));
    }
}
