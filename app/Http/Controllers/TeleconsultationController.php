<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\TeleconsultationMessage;
use App\Models\TeleconsultationSession;
use App\Models\TeleconsultationFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class TeleconsultationController extends Controller
{
    protected function authorizeAccess(Consultation $consultation)
    {
        $user = Auth::user();
        if (!$user || ($user->id !== $consultation->patient_id && $user->id !== $consultation->professionnel_id)) {
            abort(403, 'Accès refusé à cette téléconsultation');
        }
    }

    protected function ensureSession(Consultation $consultation): TeleconsultationSession
    {
        return TeleconsultationSession::firstOrCreate(
            ['consultation_id' => $consultation->id],
            [
                'status' => 'pending',
                'provider' => config('services.teleconsultation.provider', 'jitsi'),
                'room_name' => 'consultation-' . $consultation->id,
                'patient_token' => Str::random(48),
                'practitioner_token' => Str::random(48),
                'token_expires_at' => now()->addHours(12),
            ]
        );
    }

    protected function refreshSessionTokensIfExpired(TeleconsultationSession $session): TeleconsultationSession
    {
        if (!$session->token_expires_at || $session->token_expires_at->isFuture()) {
            return $session;
        }

        $session->update([
            'patient_token' => Str::random(48),
            'practitioner_token' => Str::random(48),
            'token_expires_at' => now()->addHours(12),
        ]);

        Log::info('Teleconsultation tokens regenerated after expiry', [
            'session_id' => $session->id,
            'consultation_id' => $session->consultation_id,
        ]);

        return $session->fresh();
    }

    protected function buildProviderPayload(TeleconsultationSession $session, Consultation $consultation): array
    {
        $provider = $session->provider ?? config('services.teleconsultation.provider', 'jitsi');

        if ($provider === 'twilio') {
            $room = $session->room_name;
            $domain = config('services.teleconsultation.twilio_domain', 'video.twilio.com');
            $sid = config('services.teleconsultation.twilio_sid');
            $apiKey = config('services.teleconsultation.twilio_api_key');
            $apiSecret = config('services.teleconsultation.twilio_api_secret');
            $identity = 'user-' . Auth::id();
            $twilioToken = null;

            if ($sid && $apiKey && $apiSecret && class_exists(\Twilio\Jwt\AccessToken::class)) {
                $ttl = 3600;
                $token = new \Twilio\Jwt\AccessToken(
                    $sid,
                    $apiKey,
                    $apiSecret,
                    $ttl,
                    $identity
                );
                $grant = new \Twilio\Jwt\Grants\VideoGrant();
                $grant->setRoom($room);
                $token->addGrant($grant);
                $twilioToken = $token->toJWT();
            }

            return [
                'provider' => 'twilio',
                'room' => $room,
                'join_url' => "https://{$domain}/join/{$room}",
                'token' => $twilioToken ?? (Auth::id() === $consultation->patient_id ? $session->patient_token : $session->practitioner_token),
            ];
        }

        // Par défaut : Jitsi public
        $domain = config('services.teleconsultation.jitsi_domain', 'meet.jit.si');
        $room = $session->room_name;
        return [
            'provider' => 'jitsi',
            'room' => $room,
            'join_url' => "https://{$domain}/{$room}",
            'token' => Auth::id() === $consultation->patient_id ? $session->patient_token : $session->practitioner_token,
        ];
    }

    public function room(Consultation $consultation)
    {
        $this->authorizeAccess($consultation);
        $session = $this->refreshSessionTokensIfExpired(
            $this->ensureSession($consultation)
        )->load(['messages.sender', 'files.uploader']);

        $fileLinks = $session->files->map(function ($file) use ($consultation) {
            return [
                'file' => $file,
                'url' => URL::temporarySignedRoute(
                    'teleconsultation.file.download',
                    now()->addMinutes(15),
                    ['consultation' => $consultation->id, 'file' => $file->id]
                ),
            ];
        });

        return view('teleconsultation.room', [
            'consultation' => $consultation,
            'session' => $session,
            'fileLinks' => $fileLinks,
            'provider' => $this->buildProviderPayload($session, $consultation),
        ]);
    }

    public function join(Consultation $consultation): JsonResponse
    {
        $this->authorizeAccess($consultation);
        $session = $this->refreshSessionTokensIfExpired($this->ensureSession($consultation));

        $user = Auth::user();
        $isPractitioner = $user && $user->id === $consultation->professionnel_id;

        if ($session->status === 'pending' && !$isPractitioner) {
            Log::warning('Teleconsultation join denied (waiting room)', [
                'session_id' => $session->id,
                'user_id' => optional($user)->id,
                'consultation_id' => $consultation->id,
            ]);
            return response()->json([
                'message' => 'Salle en attente. Le professionnel doit ouvrir la session.',
                'status' => $session->status,
            ], 423);
        }

        if ($session->status === 'pending' && $isPractitioner) {
            $session->update([
                'status' => 'live',
                'started_at' => $session->started_at ?? now(),
            ]);
            Log::info('Teleconsultation started by practitioner', [
                'session_id' => $session->id,
                'consultation_id' => $consultation->id,
                'user_id' => $user->id,
            ]);
        }

        return response()->json([
            'message' => 'Joined teleconsultation',
            'session_id' => $session->id,
            'status' => $session->status,
            'room_name' => $session->room_name,
            'token' => Auth::id() === $consultation->patient_id ? $session->patient_token : $session->practitioner_token,
        ]);
    }

    public function leave(Consultation $consultation): JsonResponse
    {
        $this->authorizeAccess($consultation);
        $session = TeleconsultationSession::where('consultation_id', $consultation->id)->first();

        return response()->json([
            'message' => 'Left teleconsultation',
            'session_id' => optional($session)->id,
        ]);
    }

    public function end(Consultation $consultation): JsonResponse
    {
        $this->authorizeAccess($consultation);
        $session = TeleconsultationSession::where('consultation_id', $consultation->id)->first();

        if ($session) {
            $session->update([
                'status' => 'ended',
                'ended_at' => now(),
            ]);

            Log::info('Teleconsultation ended', [
                'session_id' => $session->id,
                'consultation_id' => $consultation->id,
                'user_id' => Auth::id(),
            ]);
        }

        return response()->json([
            'message' => 'Teleconsultation ended',
            'session_id' => optional($session)->id,
            'status' => optional($session)->status,
        ]);
    }

    public function sendMessage(Request $request, Consultation $consultation): JsonResponse
    {
        $this->authorizeAccess($consultation);
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $session = $this->ensureSession($consultation);

        $message = TeleconsultationMessage::create([
            'session_id' => $session->id,
            'sender_id' => Auth::id(),
            'body' => $validated['message'],
        ]);

        return response()->json([
            'message' => 'Message sent',
            'data' => $message,
        ]);
    }

    public function shareFile(Request $request, Consultation $consultation): JsonResponse
    {
        $this->authorizeAccess($consultation);

        $validated = $request->validate([
            'file' => ['required', 'file', 'max:5120', 'mimes:pdf,jpg,jpeg,png'],
        ]);

        $session = $this->ensureSession($consultation);

        // limite simple du nombre de fichiers pour la session
        if ($session->files()->count() >= 20) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Limite de documents atteinte pour cette teleconsultation.',
                ], 422);
            }
            return back()->with('error', 'Limite de documents atteinte pour cette teleconsultation.')->withInput();
        }

        $path = $validated['file']->store("teleconsultations/{$session->id}", 'public');

        // Verification MIME secondaire cote serveur
        $realMime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), storage_path('app/public/' . $path));
        $allowedMime = ['application/pdf', 'image/jpeg', 'image/png'];
        if (!in_array($realMime, $allowedMime, true)) {
            // suppression proactive si type reelement interdit
            @unlink(storage_path('app/public/' . $path));
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Type de fichier non autorise apres verification.',
                ], 422);
            }
            return back()->with('error', 'Type de fichier non autorise apres verification.')->withInput();
        }

        $file = TeleconsultationFile::create([
            'session_id' => $session->id,
            'uploader_id' => Auth::id(),
            'original_name' => $validated['file']->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $validated['file']->getClientMimeType(),
            'size' => $validated['file']->getSize(),
        ]);

        $signedUrl = URL::temporarySignedRoute(
            'teleconsultation.file.download',
            now()->addMinutes(15),
            ['consultation' => $consultation->id, 'file' => $file->id]
        );

        Log::info('Teleconsultation file uploaded', [
            'session_id' => $session->id,
            'consultation_id' => $consultation->id,
            'file_id' => $file->id,
            'uploader_id' => Auth::id(),
            'mime' => $file->mime_type,
            'size' => $file->size,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'File uploaded',
                'data' => $file,
                'url' => $signedUrl,
            ]);
        }

        return back()->with('success', 'Document ajoute.')->with('file_url', $signedUrl);
    }

    public function downloadFile(Request $request, Consultation $consultation, TeleconsultationFile $file)
    {
        $this->authorizeAccess($consultation);

        if ($file->session->consultation_id !== $consultation->id) {
            abort(403);
        }

        if (!$request->hasValidSignature()) {
            abort(403, 'Lien expiré');
        }

        return response()->download(storage_path('app/public/' . $file->path), $file->original_name);
    }
}
