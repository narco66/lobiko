<?php

namespace Tests\Feature\Authorization;

use App\Models\Facture;
use App\Models\User;
use App\Policies\FacturePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacturePolicyTest extends TestCase
{
    use RefreshDatabase;

    private function makeFacture(User $patient, User $pro): Facture
    {
        return Facture::create([
            'numero_facture' => 'FAC-' . now()->format('Ymd') . '-0001',
            'patient_id' => $patient->id,
            'praticien_id' => $pro->id,
            'montant_ht' => 100,
            'montant_tva' => 0,
            'montant_ttc' => 100,
            'montant_final' => 100,
            'part_patient' => 100,
            'reste_a_charge' => 100,
            'montant_paye' => 0,
            'montant_restant' => 100,
            'statut_paiement' => 'en_attente',
            'date_facture' => now()->toDateString(),
            'date_echeance' => now()->addDays(30)->toDateString(),
            'type' => 'consultation',
            'nature' => 'normale',
        ]);
    }

    public function test_patient_peut_voir_sa_facture(): void
    {
        $patient = User::factory()->create();
        $pro = User::factory()->create();
        $facture = $this->makeFacture($patient, $pro);
        $policy = new FacturePolicy();

        $this->assertTrue($policy->view($patient, $facture));
    }

    public function test_autre_patient_ne_peut_pas_voir_facture(): void
    {
        $patient = User::factory()->create();
        $other = User::factory()->create();
        $pro = User::factory()->create();
        $facture = $this->makeFacture($patient, $pro);
        $policy = new FacturePolicy();

        $this->assertFalse($policy->view($other, $facture));
    }

    public function test_admin_peut_modifier_facture(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $patient = User::factory()->create();
        $pro = User::factory()->create();
        $facture = $this->makeFacture($patient, $pro);
        $policy = new FacturePolicy();

        $this->assertTrue($policy->update($admin, $facture));
    }
}
