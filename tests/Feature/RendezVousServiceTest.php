<?php

namespace Tests\Feature;

use App\Models\RendezVous;
use App\Models\StructureMedicale;
use App\Models\User;
use App\Services\RendezVousService;
use Database\Seeders\TestsBaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class RendezVousServiceTest extends TestCase
{
    use RefreshDatabase;

    protected RendezVousService $service;
    protected User $patient;
    protected User $professionnel;
    protected StructureMedicale $structure;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TestsBaseSeeder::class);

        $this->patient = User::factory()->patient()->create();
        $this->professionnel = User::factory()->medecin()->create();
        $this->structure = StructureMedicale::factory()->create();
        $this->service = app(RendezVousService::class);
    }

    public function test_creer_rdv_sans_collision(): void
    {
        $rdv = $this->service->creer([
            'patient_id' => $this->patient->id,
            'professionnel_id' => $this->professionnel->id,
            'structure_id' => $this->structure->id,
            'date_heure' => now()->addDay()->setTime(9, 0),
            'duree_prevue' => 30,
            'type' => 'consultation',
            'modalite' => 'presentiel',
            'specialite' => 'GǸnǸrale',
            'motif' => 'test',
        ]);

        $this->assertEquals('en_attente', $rdv->statut);
        $this->assertEquals($this->patient->id, $rdv->patient_id);
    }

    public function test_collision_rdv_pro(): void
    {
        $start = now()->addDay()->setTime(10, 0);
        RendezVous::factory()->create([
            'professionnel_id' => $this->professionnel->id,
            'patient_id' => $this->patient->id,
            'structure_id' => $this->structure->id,
            'date_heure' => $start,
            'date_heure_fin' => $start->copy()->addMinutes(30),
        ]);

        $this->expectException(\RuntimeException::class);

        $this->service->creer([
            'patient_id' => User::factory()->patient()->create()->id,
            'professionnel_id' => $this->professionnel->id,
            'structure_id' => $this->structure->id,
            'date_heure' => $start->copy()->addMinutes(15),
            'duree_prevue' => 30,
            'type' => 'consultation',
            'modalite' => 'presentiel',
            'specialite' => 'GǸnǸrale',
            'motif' => 'test',
        ]);
    }

    public function test_annuler_rdv(): void
    {
        $rdv = RendezVous::factory()->create([
            'professionnel_id' => $this->professionnel->id,
            'patient_id' => $this->patient->id,
            'structure_id' => $this->structure->id,
            'statut' => 'confirme',
        ]);

        $this->service->annuler($rdv, 'patient indisponible', $this->patient->id);

        $this->assertEquals('annule', $rdv->fresh()->statut);
        $this->assertNotNull($rdv->fresh()->annule_at);
    }

    public function test_reprogrammer_rdv(): void
    {
        $rdv = RendezVous::factory()->create([
            'professionnel_id' => $this->professionnel->id,
            'patient_id' => $this->patient->id,
            'structure_id' => $this->structure->id,
            'date_heure' => now()->addDay()->setTime(9, 0),
            'date_heure_fin' => now()->addDay()->setTime(9, 30),
            'statut' => 'confirme',
        ]);

        $nouveau = $this->service->reprogrammer($rdv, Carbon::parse(now()->addDay()->setTime(11, 0)), 30);

        $this->assertEquals('confirme', $nouveau->statut);
        $this->assertEquals($rdv->id, $nouveau->reporte_de);
        $this->assertEquals('reporte', $rdv->fresh()->statut);
    }
}
