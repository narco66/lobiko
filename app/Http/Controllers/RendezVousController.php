<?php

namespace App\Http\Controllers;

use App\Http\Requests\RendezVousRequest;
use App\Models\AppointmentRequest;
use App\Models\RendezVous;
use App\Models\StructureMedicale;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\SmsNotifier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class RendezVousController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $search = $request->get('search');
        $statut = $request->get('statut');
        $du = $request->get('du');
        $au = $request->get('au');

        $query = RendezVous::with(['patient:id,name,email', 'professionnel:id,name', 'structure:id,nom_structure'])
            ->when($user && $user->hasRole('medecin'), fn ($q) => $q->where('professionnel_id', $user->id))
            ->when($user && $user->hasRole('patient'), fn ($q) => $q->where('patient_id', $user->id))
            ->when($search, function ($q) use ($search) {
                $q->where('numero_rdv', 'like', "%{$search}%")
                    ->orWhereHas('patient', fn ($p) => $p->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('professionnel', fn ($p) => $p->where('name', 'like', "%{$search}%"));
            })
            ->when($statut, fn ($q) => $q->where('statut', $statut))
            ->when($du, fn ($q) => $q->whereDate('date_heure', '>=', $du))
            ->when($au, fn ($q) => $q->whereDate('date_heure', '<=', $au))
            ->orderBy('date_heure', 'asc');

        $upcoming = $query->paginate(15)->withQueryString();

        // Stats rapides
        $stats = [
            'total' => RendezVous::count(),
            'aujourd_hui' => RendezVous::whereDate('date_heure', today())->count(),
            'confirmes' => RendezVous::where('statut', 'confirme')->count(),
            'en_attente' => RendezVous::where('statut', 'en_attente')->count(),
        ];

        return view('appointments.index', compact('upcoming', 'stats'));
    }

    public function create(Request $request)
    {
        $structures = StructureMedicale::select('id', 'nom_structure', 'adresse_ville')->orderBy('nom_structure')->limit(100)->get();

        $practitioners = User::select('id', 'name', 'prenom', 'nom', 'email')
            ->orderBy('name')
            ->limit(200)
            ->get()
            ->mapWithKeys(function ($u) {
                $label = trim($u->name ?: "{$u->prenom} {$u->nom}") ?: ($u->email ?? 'Praticien');
                return [$u->id => $label];
            });

        $patients = User::select('id', 'name', 'prenom', 'nom', 'email')
            ->orderBy('name')
            ->limit(200)
            ->get()
            ->mapWithKeys(function ($u) {
                $label = trim($u->name ?: "{$u->prenom} {$u->nom}") ?: ($u->email ?? 'Patient');
                return [$u->id => $label];
            });

        $selectedStructureId = $request->query('structure_id');
        $selectedPractitionerId = $request->query('practitioner_id');

        return view('appointments.create', compact(
            'structures',
            'practitioners',
            'patients',
            'selectedStructureId',
            'selectedPractitionerId'
        ));
    }

    public function store(RendezVousRequest $request)
    {
        $data = $request->validated();
        if (empty($data['lieu_type'])) {
            $data['lieu_type'] = match ($data['modalite']) {
                'teleconsultation' => 'visio',
                'domicile' => 'domicile',
                default => 'cabinet',
            };
        }
        $data['numero_rdv'] = $data['numero_rdv'] ?? RendezVous::generateNumero();
        $data['statut'] = $data['statut'] ?? 'en_attente';

        $rdv = RendezVous::create($data);

        // Notification simple
        $notifier = app(NotificationService::class);
        $notifier->notifyUser($rdv->patient_id, 'Rendez-vous créé', "Votre rendez-vous {$rdv->numero_rdv} est enregistré.");

        return redirect()->route('appointments.show', $rdv)->with('success', 'Rendez-vous créé');
    }

    public function show(RendezVous $appointment)
    {
        $appointment->load(['patient:id,name,email', 'professionnel:id,name', 'structure:id,nom_structure']);
        return view('appointments.show', ['rdv' => $appointment]);
    }

    public function edit(RendezVous $appointment)
    {
        $structures = StructureMedicale::select('id', 'nom_structure', 'adresse_ville')->orderBy('nom_structure')->limit(100)->get();

        $practitioners = User::select('id', 'name', 'prenom', 'nom', 'email')
            ->orderBy('name')
            ->limit(200)
            ->get()
            ->mapWithKeys(function ($u) {
                $label = trim($u->name ?: "{$u->prenom} {$u->nom}") ?: ($u->email ?? 'Praticien');
                return [$u->id => $label];
            });

        $patients = User::select('id', 'name', 'prenom', 'nom', 'email')
            ->orderBy('name')
            ->limit(200)
            ->get()
            ->mapWithKeys(function ($u) {
                $label = trim($u->name ?: "{$u->prenom} {$u->nom}") ?: ($u->email ?? 'Patient');
                return [$u->id => $label];
            });

        return view('appointments.edit', [
            'rdv' => $appointment,
            'structures' => $structures,
            'practitioners' => $practitioners,
            'patients' => $patients
        ]);
    }

    public function update(RendezVousRequest $request, RendezVous $appointment)
    {
        $data = $request->validated();
        if (empty($data['lieu_type']) && !empty($data['modalite'])) {
            $data['lieu_type'] = match ($data['modalite']) {
                'teleconsultation' => 'visio',
                'domicile' => 'domicile',
                default => 'cabinet',
            };
        }
        $appointment->update($data);
        return redirect()->route('appointments.show', $appointment)->with('success', 'Rendez-vous mis à jour');
    }

    public function destroy(RendezVous $appointment)
    {
        $appointment->delete();
        return redirect()->route('appointments.index')->with('success', 'Rendez-vous supprimé');
    }

    public function storeRequest(Request $request)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email'],
            'speciality' => ['required', 'string', 'max:100'],
            'mode' => ['required', 'in:presentiel,teleconsultation,domicile'],
            'lieu_type' => ['nullable', 'in:cabinet,clinique,domicile,visio'],
            'preferred_date' => ['required', 'date'],
            'preferred_datetime' => ['nullable', 'date'],
            'structure_id' => ['nullable', 'uuid'],
            'practitioner_id' => ['nullable', 'uuid'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $numero = 'RDV-' . now()->format('Ymd') . '-' . str_pad(AppointmentRequest::count() + 1, 4, '0', STR_PAD_LEFT);
        $record = AppointmentRequest::create(array_merge($validated, ['numero_rdv' => $numero]));

        try {
            $to = config('mail.from.address') ?? env('MAIL_FROM_ADDRESS');
            if ($to) {
                Mail::to($to)->send(new \App\Mail\ServiceRequestNotification('Rendez-vous', $record->toArray()));
            }
            if (!empty($record->email)) {
                Mail::to($record->email)->send(new \App\Mail\ServiceRequestNotification('Rendez-vous', $record->toArray()));
            }
            $sms = app(SmsNotifier::class);
            $smsRecipient = env('ADMIN_SMS_TO');
            $sms->send($smsRecipient, "[Rendez-vous {$record->numero_rdv}] {$record->full_name} ({$record->phone})");
            $sms->send($record->phone, "Votre demande RDV {$record->numero_rdv} est bien reçue. Nous vous contacterons pour confirmation.");
        } catch (\Throwable $e) {
            // Ignorer pour ne pas bloquer
        }

        session()->flash('appointment_request', $record->toArray());

        return redirect()->route('appointments.thanks');
    }

    public function thanks()
    {
        $data = session('appointment_request');
        return view('appointments.thanks', ['data' => $data]);
    }
}
