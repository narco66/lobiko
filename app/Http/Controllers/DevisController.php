<?php

namespace App\Http\Controllers;

use App\Http\Requests\DevisStoreRequest;
use App\Http\Requests\DevisUpdateRequest;
use App\Models\Devis;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DevisController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Devis::class);

        $search = $request->get('search');
        $statut = $request->get('statut');
        $du = $request->get('du');
        $au = $request->get('au');

        $devis = Devis::with(['patient:id,name', 'praticien:id,name'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('numero_devis', 'like', "%{$search}%")
                        ->orWhereHas('patient', fn ($p) => $p->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('praticien', fn ($p) => $p->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($statut, fn ($q) => $q->where('statut', $statut))
            ->when($du, fn ($q) => $q->whereDate('date_emission', '>=', $du))
            ->when($au, fn ($q) => $q->whereDate('date_emission', '<=', $au))
            ->orderByDesc('date_emission')
            ->paginate(15)
            ->withQueryString();

        return view('devis.index', compact('devis'));
    }

    public function create()
    {
        Gate::authorize('create', Devis::class);

        [$patients, $praticiens] = $this->usersForForms();

        return view('devis.create', compact('patients', 'praticiens'));
    }

    public function store(DevisStoreRequest $request)
    {
        Gate::authorize('create', Devis::class);

        $devis = Devis::create($this->prepareData($request->validated()));

        return redirect()
            ->route('admin.devis.show', $devis)
            ->with('success', 'Devis cree avec succes.');
    }

    public function show(Devis $devis)
    {
        Gate::authorize('view', $devis);

        $devis->load(['patient:id,name', 'praticien:id,name']);

        return view('devis.show', compact('devis'));
    }

    public function edit(Devis $devis)
    {
        Gate::authorize('update', $devis);

        [$patients, $praticiens] = $this->usersForForms($devis);
        $devis->load(['patient:id,name', 'praticien:id,name']);

        return view('devis.edit', compact('devis', 'patients', 'praticiens'));
    }

    public function update(DevisUpdateRequest $request, Devis $devis)
    {
        Gate::authorize('update', $devis);

        $devis->update($this->prepareData($request->validated()));

        return redirect()
            ->route('admin.devis.show', $devis)
            ->with('success', 'Devis mis a jour.');
    }

    public function destroy(Devis $devis)
    {
        Gate::authorize('delete', $devis);

        $devis->delete();

        return redirect()
            ->route('admin.devis.index')
            ->with('success', 'Devis supprime.');
    }

    private function prepareData(array $data): array
    {
        $data['numero_devis'] = $data['numero_devis'] ?: null;
        $data['statut'] = $data['statut'] ?? 'brouillon';
        $dateEmission = $data['date_emission'] ?? now();
        $dateEmission = $dateEmission instanceof \Carbon\Carbon ? $dateEmission : \Carbon\Carbon::parse($dateEmission);
        $data['date_emission'] = $dateEmission;
        if (empty($data['date_validite'])) {
            $days = $data['duree_validite'] ?? 30;
            $data['date_validite'] = $dateEmission->copy()->addDays($days);
        }
        $data['montant_ht'] = (float) ($data['montant_ht'] ?? $data['montant_final']);
        $data['montant_tva'] = (float) ($data['montant_tva'] ?? 0);
        $data['montant_ttc'] = (float) ($data['montant_ttc'] ?? $data['montant_final']);
        $data['montant_remise'] = (float) ($data['montant_remise'] ?? 0);
        $data['montant_majoration'] = (float) ($data['montant_majoration'] ?? 0);
        $data['montant_assurance'] = (float) ($data['montant_assurance'] ?? 0);
        $data['reste_a_charge'] = (float) ($data['reste_a_charge'] ?? $data['montant_final']);
        $data['simulation_pec'] = (bool) ($data['simulation_pec'] ?? false);

        return $data;
    }

    private function usersForForms(Devis $devis = null): array
    {
        $patients = User::select('id', 'name')
            ->orderBy('name')
            ->limit(100)
            ->get();

        $praticiens = User::select('id', 'name')
            ->orderBy('name')
            ->limit(100)
            ->get();

        if ($devis) {
            if ($devis->patient && !$patients->contains('id', $devis->patient_id)) {
                $patients->push($devis->patient);
            }
            if ($devis->praticien && !$praticiens->contains('id', $devis->praticien_id)) {
                $praticiens->push($devis->praticien);
            }
        }

        return [$patients, $praticiens];
    }
}
