<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientStoreRequest;
use App\Http\Requests\PatientUpdateRequest;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', User::class);

        $search = $request->get('search');

        $patients = User::role('patient')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nom', 'like', "%{$search}%")
                        ->orWhere('prenom', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('telephone', 'like', "%{$search}%");
                });
            })
            ->orderBy('nom')
            ->paginate(15)
            ->withQueryString();

        return view('patients.index', compact('patients'));
    }

    public function create()
    {
        Gate::authorize('create', User::class);

        return view('patients.create');
    }

    public function store(PatientStoreRequest $request)
    {
        Gate::authorize('create', User::class);

        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $data['statut_compte'] = $data['statut_compte'] ?? 'actif';
        $data['notifications_sms'] = (bool) ($data['notifications_sms'] ?? false);
        $data['notifications_email'] = (bool) ($data['notifications_email'] ?? true);
        $data['notifications_push'] = (bool) ($data['notifications_push'] ?? true);

        $patient = User::create($data);
        $patient->syncRoles(['patient']);

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', 'Patient cree avec succes.');
    }

    public function show(User $patient)
    {
        Gate::authorize('view', $patient);

        $patient->load(['dossierMedical', 'contratAssuranceActif']);
        $stats = $patient->getPatientStats();

        return view('patients.show', compact('patient', 'stats'));
    }

    public function edit(User $patient)
    {
        Gate::authorize('update', $patient);

        return view('patients.edit', compact('patient'));
    }

    public function update(PatientUpdateRequest $request, User $patient)
    {
        Gate::authorize('update', $patient);

        $data = $request->validated();
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['notifications_sms'] = (bool) ($data['notifications_sms'] ?? false);
        $data['notifications_email'] = (bool) ($data['notifications_email'] ?? true);
        $data['notifications_push'] = (bool) ($data['notifications_push'] ?? true);

        $patient->update($data);
        $patient->syncRoles(['patient']);

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', 'Patient mis a jour.');
    }

    public function destroy(User $patient)
    {
        Gate::authorize('delete', $patient);

        $patient->delete();

        return redirect()
            ->route('patients.index')
            ->with('success', 'Patient archive.');
    }
}
