<?php

namespace Tests\Feature;

use App\Models\DossierMedical;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DossierMedicalTest extends TestCase
{
    use RefreshDatabase;

    public function test_medecin_peut_creer_et_voir_un_dossier(): void
    {
        $medecin = User::factory()->create();
        $medecin->assignRole('medecin');
        $patient = User::factory()->create();

        $payload = [
            'patient_id' => $patient->id,
            'numero_dossier' => 'DME-' . now()->year . '-000001',
            'allergies' => ['pollen'],
        ];

        $this->actingAs($medecin)
            ->post(route('dossiers-medicaux.store'), $payload)
            ->assertRedirect();

        $dossier = DossierMedical::first();
        $this->assertNotNull($dossier);

        $this->actingAs($medecin)
            ->get(route('dossiers-medicaux.show', $dossier))
            ->assertOk()
            ->assertSee($dossier->numero_dossier);
    }

    public function test_patient_peut_voir_son_dossier_mais_pas_un_autre(): void
    {
        $patient = User::factory()->create();
        $other = User::factory()->create();

        $dossier = DossierMedical::create([
            'patient_id' => $patient->id,
            'numero_dossier' => 'DME-' . now()->year . '-000002',
        ]);

        $this->actingAs($patient)
            ->get(route('dossiers-medicaux.show', $dossier))
            ->assertOk();

        $this->actingAs($other)
            ->get(route('dossiers-medicaux.show', $dossier))
            ->assertForbidden();
    }
}
