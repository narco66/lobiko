<?php

namespace App\Services;

use App\Models\Consultation;
use App\Models\TeleconsultationSession;
use Illuminate\Support\Str;

class ConsultationService
{
    /**
     * Crée ou récupère une session de téléconsultation pour une consultation donnée.
     */
    public function createTeleconsultationSession(Consultation $consultation): TeleconsultationSession
    {
        return TeleconsultationSession::firstOrCreate(
            ['consultation_id' => $consultation->id],
            [
                'status' => 'pending',
                'provider' => config('services.teleconsultation.provider', 'jitsi'),
                'room_name' => 'consultation-' . $consultation->id,
                'patient_token' => Str::random(64),
                'practitioner_token' => Str::random(64),
                'token_expires_at' => now()->addHours(12),
            ]
        );
    }
}
