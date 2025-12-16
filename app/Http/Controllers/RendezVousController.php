<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\RendezVous;
use Illuminate\Support\Carbon;
use App\Models\AppointmentRequest;
use App\Mail\ServiceRequestNotification;
use Illuminate\Support\Facades\Mail;
use App\Services\SmsNotifier;
use App\Models\StructureMedicale;
use App\Models\User;

class RendezVousController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $upcoming = [];

        $records = RendezVous::query()
            ->orderBy('date_heure')
            ->whereDate('date_heure', '>=', now()->toDateString())
            ->limit(10)
            ->get();

        if ($records->isNotEmpty()) {
            $upcoming = $records->map(function ($rdv) {
                return [
                    'date' => Carbon::parse($rdv->date_heure)->format('d/m/Y H:i'),
                    'doctor' => optional($rdv->professionnel)->prenom ?? 'Praticien',
                    'mode' => ucfirst($rdv->modalite),
                    'status' => ucfirst($rdv->statut),
                ];
            })->toArray();
        }

        // Fallback si aucune donnée en base ou si la table est vide
        if (count($upcoming) === 0) {
            $upcoming = [
                [
                    'date' => now()->addDay()->format('d/m/Y H:i'),
                    'doctor' => 'Dr. Marie Owono',
                    'mode' => 'Visio',
                    'status' => 'Confirmé',
                ],
                [
                    'date' => now()->addDays(2)->format('d/m/Y H:i'),
                    'doctor' => 'Dr. Serge Mba',
                    'mode' => 'Présentiel',
                    'status' => 'En attente',
                ],
            ];
        }

        return view('appointments.index', compact('upcoming'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $structures = StructureMedicale::select('id', 'nom_structure', 'adresse_ville')->orderBy('nom_structure')->limit(50)->get();
        $practitioners = User::select('id', 'nom', 'prenom')->orderBy('prenom')->limit(50)->get();

        $selectedStructureId = $request->query('structure_id');
        $selectedPractitionerId = $request->query('practitioner_id');

        return view('appointments.create', compact('structures', 'practitioners', 'selectedStructureId', 'selectedPractitionerId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email'],
            'speciality' => ['required', 'string', 'max:100'],
            'mode' => ['required', 'in:presentiel,teleconsultation,domicile'],
            'preferred_date' => ['required', 'date'],
            'preferred_datetime' => ['nullable', 'date'],
            'structure_id' => ['nullable', 'uuid'],
            'practitioner_id' => ['nullable', 'uuid'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $numero = 'RDV-' . now()->format('Ymd') . '-' . str_pad(AppointmentRequest::count() + 1, 4, '0', STR_PAD_LEFT);
        $record = AppointmentRequest::create(array_merge($validated, ['numero_rdv' => $numero]));

        // Notifications (mail + SMS)
        try {
            $to = config('mail.from.address') ?? env('MAIL_FROM_ADDRESS');
            if ($to) {
                Mail::to($to)->send(new ServiceRequestNotification('Rendez-vous', $record->toArray()));
            }
            if (!empty($record->email)) {
                Mail::to($record->email)->send(new ServiceRequestNotification('Rendez-vous', $record->toArray()));
            }
            $sms = app(SmsNotifier::class);
            $smsRecipient = env('ADMIN_SMS_TO');
            $sms->send($smsRecipient, "[Rendez-vous {$record->numero_rdv}] {$record->full_name} ({$record->phone})");
            // Confirmation SMS patient si téléphone
            $sms->send($record->phone, "Votre demande RDV {$record->numero_rdv} est bien reçue. Nous vous contacterons pour confirmation.");
        } catch (\Throwable $e) {
            // On ignore pour ne pas casser le parcours.
        }

        Session::flash('appointment_request', $record->toArray());

        return redirect()->route('appointments.thanks');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function thanks()
    {
        $data = session('appointment_request');
        return view('appointments.thanks', ['data' => $data]);
    }
}
