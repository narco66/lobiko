<?php

namespace App\Http\Controllers;

use App\Http\Requests\FactureStoreRequest;
use App\Http\Requests\FactureUpdateRequest;
use App\Models\User;
use App\Models\Facture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class FactureController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Facture::class);

        $search = $request->get('search');
        $statut = $request->get('statut');
        $du = $request->get('du');
        $au = $request->get('au');

        $factures = Facture::with(['patient:id,name', 'praticien:id,name'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('numero_facture', 'like', "%{$search}%")
                        ->orWhereHas('patient', fn ($p) => $p->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('praticien', fn ($p) => $p->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($statut, fn ($q) => $q->where('statut_paiement', $statut))
            ->when($du, fn ($q) => $q->whereDate('created_at', '>=', $du))
            ->when($au, fn ($q) => $q->whereDate('created_at', '<=', $au))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('factures.index', compact('factures'));
    }

    public function create()
    {
        Gate::authorize('create', Facture::class);

        [$patients, $praticiens] = $this->usersForForms();

        return view('factures.create', compact('patients', 'praticiens'));
    }

    public function store(FactureStoreRequest $request)
    {
        Gate::authorize('create', Facture::class);

        $facture = Facture::create($this->prepareData($request->validated()));

        return redirect()
            ->route('admin.factures.show', $facture)
            ->with('success', 'Facture créée avec succès.');
    }

    public function show(Facture $facture)
    {
        Gate::authorize('view', $facture);

        $facture->load(['patient:id,name', 'praticien:id,name']);

        return view('factures.show', compact('facture'));
    }

    public function edit(Facture $facture)
    {
        Gate::authorize('update', $facture);

        [$patients, $praticiens] = $this->usersForForms($facture);
        $facture->load(['patient:id,name', 'praticien:id,name']);

        return view('factures.edit', compact('facture', 'patients', 'praticiens'));
    }

    public function update(FactureUpdateRequest $request, Facture $facture)
    {
        Gate::authorize('update', $facture);

        $facture->update($this->prepareData($request->validated()));

        return redirect()
            ->route('admin.factures.show', $facture)
            ->with('success', 'Facture mise à jour.');
    }

    public function destroy(Facture $facture)
    {
        Gate::authorize('delete', $facture);

        $facture->delete();

        return redirect()
            ->route('admin.factures.index')
            ->with('success', 'Facture supprimée.');
    }

    private function prepareData(array $data): array
    {
        $data['numero_facture'] = $data['numero_facture'] ?: null;
        $data['statut_paiement'] = $data['statut_paiement'] ?? 'en_attente';
        if ($data['statut_paiement'] === 'payee') {
            $data['statut_paiement'] = 'paye';
        }
        if ($data['statut_paiement'] === 'partiellement_payee') {
            $data['statut_paiement'] = 'partiel';
        }
        if ($data['statut_paiement'] === 'annulee') {
            $data['statut_paiement'] = 'annule';
        }
        if (in_array($data['statut_paiement'], ['brouillon', 'envoyee'], true)) {
            $data['statut_paiement'] = 'en_attente';
        }
        $data['type'] = $data['type'] ?? 'consultation';
        $data['nature'] = $data['nature'] ?? 'normale';
        $data['montant_final'] = (float) ($data['montant_final'] ?? 0);
        $data['montant_ht'] = (float) ($data['montant_ht'] ?? $data['montant_final']);
        $data['montant_tva'] = (float) ($data['montant_tva'] ?? 0);
        $data['montant_ttc'] = (float) ($data['montant_ttc'] ?? $data['montant_final']);
        $data['montant_remise'] = (float) ($data['montant_remise'] ?? 0);
        $data['montant_majoration'] = (float) ($data['montant_majoration'] ?? 0);
        $data['part_patient'] = (float) ($data['part_patient'] ?? $data['montant_final']);
        $data['part_assurance'] = (float) ($data['part_assurance'] ?? 0);
        $data['part_subvention'] = (float) ($data['part_subvention'] ?? 0);
        $data['reste_a_charge'] = (float) ($data['reste_a_charge'] ?? $data['part_patient']);
        $data['montant_paye'] = (float) ($data['montant_paye'] ?? 0);
        $data['montant_pec'] = (float) ($data['montant_pec'] ?? 0);
        $data['montant_restant'] = max($data['montant_final'] - $data['montant_paye'], 0);
        $data['date_facture'] = $data['date_facture'] ?? now();

        return $data;
    }

    private function usersForForms(Facture $facture = null): array
    {
        $patients = User::select('id', 'name')
            ->orderBy('name')
            ->limit(100)
            ->get();

        $praticiens = User::select('id', 'name')
            ->orderBy('name')
            ->limit(100)
            ->get();

        if ($facture) {
            if ($facture->patient && !$patients->contains('id', $facture->patient_id)) {
                $patients->push($facture->patient);
            }
            if ($facture->praticien && !$praticiens->contains('id', $facture->praticien_id)) {
                $praticiens->push($facture->praticien);
            }
        }

        return [$patients, $praticiens];
    }
}
