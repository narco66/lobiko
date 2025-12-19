<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PartnerController extends Controller
{
    public function index()
    {
        $partners = Partner::orderBy('name')->paginate(20);
        return view('partners.index', compact('partners'));
    }

    public function create()
    {
        Gate::authorize('create', Partner::class);
        return view('partners.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Partner::class);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'partner_type' => ['required', 'in:ASSUREUR,PHARMACIE,STRUCTURE_MEDICALE,AUTRE'],
            'statut' => ['required', 'in:actif,suspendu,en_attente'],
            'type' => ['nullable', 'in:payment,insurance,medical,logistics,technology,other'],
            'commission_mode' => ['nullable', 'in:percent,fixed,none'],
            'commission_value' => ['nullable', 'numeric'],
            'contact_email' => ['nullable', 'email'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'adresse_ville' => ['nullable', 'string', 'max:255'],
            'adresse_pays' => ['nullable', 'string', 'max:255'],
            'numero_legal' => ['nullable', 'string', 'max:255'],
        ]);
        // Backward compat: mapper partner_type -> type (catégorie front existante)
        $data['type'] = $data['type'] ?? match ($data['partner_type']) {
            'ASSUREUR' => 'insurance',
            'PHARMACIE', 'STRUCTURE_MEDICALE' => 'medical',
            default => 'other',
        };
        Partner::create($data);
        return redirect()->route('partners')->with('success', 'Partenaire créé.');
    }

    public function edit(Partner $partner)
    {
        Gate::authorize('update', $partner);
        return view('partners.edit', compact('partner'));
    }

    public function update(Request $request, Partner $partner)
    {
        Gate::authorize('update', $partner);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'partner_type' => ['required', 'in:ASSUREUR,PHARMACIE,STRUCTURE_MEDICALE,AUTRE'],
            'statut' => ['required', 'in:actif,suspendu,en_attente'],
            'type' => ['nullable', 'in:payment,insurance,medical,logistics,technology,other'],
            'commission_mode' => ['nullable', 'in:percent,fixed,none'],
            'commission_value' => ['nullable', 'numeric'],
            'contact_email' => ['nullable', 'email'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'adresse_ville' => ['nullable', 'string', 'max:255'],
            'adresse_pays' => ['nullable', 'string', 'max:255'],
            'numero_legal' => ['nullable', 'string', 'max:255'],
        ]);
        $data['type'] = $data['type'] ?? match ($data['partner_type']) {
            'ASSUREUR' => 'insurance',
            'PHARMACIE', 'STRUCTURE_MEDICALE' => 'medical',
            default => 'other',
        };
        $partner->update($data);
        return redirect()->route('partners')->with('success', 'Partenaire mis à jour.');
    }

    public function destroy(Partner $partner)
    {
        Gate::authorize('delete', $partner);
        $partner->delete();
        return redirect()->route('partners')->with('success', 'Partenaire supprimé.');
    }
}
