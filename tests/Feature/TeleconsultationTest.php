<?php

namespace Tests\Feature;

use App\Models\Consultation;
use App\Models\TeleconsultationFile;
use App\Models\TeleconsultationSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class TeleconsultationTest extends TestCase
{
    use RefreshDatabase;

    private function makeConsultation(User $patient, User $pro): Consultation
    {
        return Consultation::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'numero_consultation' => 'CONS-' . now()->format('Ymd') . '-0001',
            'patient_id' => $patient->id,
            'professionnel_id' => $pro->id,
            'date_consultation' => now(),
            'heure_debut' => now()->format('H:i:s'),
            'type' => 'generale',
            'modalite' => 'teleconsultation',
            'motif_consultation' => 'Test',
            'diagnostic_principal' => 'Test',
            'conduite_a_tenir' => 'Repos',
        ]);
    }

    public function test_patient_can_view_room(): void
    {
        $patient = User::factory()->create();
        $pro = User::factory()->create();
        $consultation = $this->makeConsultation($patient, $pro);
        TeleconsultationSession::create([
            'consultation_id' => $consultation->id,
            'status' => 'pending',
            'provider' => 'jitsi',
            'room_name' => 'consultation-' . $consultation->id,
            'patient_token' => 'tok-patient',
            'practitioner_token' => 'tok-pro',
        ]);

        $this->actingAs($patient)
            ->get(route('teleconsultation.room', $consultation))
            ->assertOk()
            ->assertSee($consultation->id)
            ->assertSee('Salle virtuelle');
    }

    public function test_join_sets_live_and_returns_token(): void
    {
        $patient = User::factory()->create();
        $pro = User::factory()->create();
        $consultation = $this->makeConsultation($patient, $pro);
        TeleconsultationSession::create([
            'consultation_id' => $consultation->id,
            'status' => 'pending',
            'provider' => 'jitsi',
            'room_name' => 'consultation-' . $consultation->id,
            'patient_token' => 'tok-patient',
            'practitioner_token' => 'tok-pro',
        ]);

        $this->actingAs($patient)
            ->postJson(route('teleconsultation.join', $consultation))
            ->assertOk()
            ->assertJsonFragment(['status' => 'live'])
            ->assertJsonFragment(['token' => 'tok-patient']);
    }

    public function test_tokens_regenerate_after_expiry(): void
    {
        $patient = User::factory()->create();
        $pro = User::factory()->create();
        $consultation = $this->makeConsultation($patient, $pro);
        $session = TeleconsultationSession::create([
            'consultation_id' => $consultation->id,
            'status' => 'pending',
            'provider' => 'jitsi',
            'room_name' => 'consultation-' . $consultation->id,
            'patient_token' => 'tok-expired',
            'practitioner_token' => 'tok-pro',
            'token_expires_at' => now()->subMinutes(5),
        ]);

        $response = $this->actingAs($patient)
            ->postJson(route('teleconsultation.join', $consultation))
            ->assertOk()
            ->assertJsonFragment(['status' => 'live']);

        $this->assertNotEquals('tok-expired', $response->json('token'));
        $this->assertNotEquals('tok-expired', $session->fresh()->patient_token);
    }

    public function test_unauthorized_user_cannot_join(): void
    {
        $patient = User::factory()->create();
        $pro = User::factory()->create();
        $consultation = $this->makeConsultation($patient, $pro);
        TeleconsultationSession::create([
            'consultation_id' => $consultation->id,
            'status' => 'pending',
            'provider' => 'jitsi',
            'room_name' => 'consultation-' . $consultation->id,
            'patient_token' => 'tok-patient',
            'practitioner_token' => 'tok-pro',
        ]);

        $this->actingAs(User::factory()->create())
            ->postJson(route('teleconsultation.join', $consultation))
            ->assertForbidden();
    }

    public function test_signed_file_download(): void
    {
        $patient = User::factory()->create();
        $pro = User::factory()->create();
        $consultation = $this->makeConsultation($patient, $pro);
        $session = TeleconsultationSession::create([
            'consultation_id' => $consultation->id,
            'status' => 'live',
            'provider' => 'jitsi',
            'room_name' => 'consultation-' . $consultation->id,
            'patient_token' => 'tok-patient',
            'practitioner_token' => 'tok-pro',
        ]);

        $path = "teleconsultations/{$session->id}/test.pdf";
        Storage::disk('public')->put($path, 'content');

        $file = TeleconsultationFile::create([
            'session_id' => $session->id,
            'uploader_id' => $patient->id,
            'original_name' => 'test.pdf',
            'path' => $path,
            'mime_type' => 'application/pdf',
            'size' => 7,
        ]);

        $signedUrl = URL::temporarySignedRoute(
            'teleconsultation.file.download',
            now()->addMinutes(5),
            ['consultation' => $consultation->id, 'file' => $file->id]
        );

        $this->actingAs($patient)
            ->get($signedUrl)
            ->assertOk()
            ->assertHeader('content-disposition');
    }
}
