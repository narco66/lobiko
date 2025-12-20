<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssuranceStoreRequest;
use App\Http\Requests\AssuranceUpdateRequest;
use App\Models\CompagnieAssurance;
use Illuminate\Support\Facades\Gate;

class AssuranceController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', CompagnieAssurance::class);

        $assurances = CompagnieAssurance::query()
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nom_assureur', 'like', "%{$search}%")
                        ->orWhere('nom_commercial', 'like', "%{$search}%")
                        ->orWhere('code_assureur', 'like', "%{$search}%")
                        ->orWhere('ville', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('nom_assureur')
            ->paginate(15)
            ->withQueryString();

        return view('assurances.index', compact('assurances'));
    }

    public function create()
    {
        Gate::authorize('create', CompagnieAssurance::class);
        return view('assurances.create');
    }

    public function store(AssuranceStoreRequest $request)
    {
        Gate::authorize('create', CompagnieAssurance::class);
        $data = $this->sanitize($request->validated());

        $assurance = CompagnieAssurance::create($data);

        return redirect()
            ->route('admin.assurances.show', $assurance)
            ->with('success', 'Assureur créé avec succès.');
    }

    public function show(CompagnieAssurance $assurance)
    {
        Gate::authorize('view', $assurance);
        return view('assurances.show', compact('assurance'));
    }

    public function edit(CompagnieAssurance $assurance)
    {
        Gate::authorize('update', $assurance);
        return view('assurances.edit', compact('assurance'));
    }

    public function update(AssuranceUpdateRequest $request, CompagnieAssurance $assurance)
    {
        Gate::authorize('update', $assurance);
        $data = $this->sanitize($request->validated());

        $assurance->update($data);

        return redirect()
            ->route('admin.assurances.show', $assurance)
            ->with('success', 'Assureur mis à jour.');
    }

    public function destroy(CompagnieAssurance $assurance)
    {
        Gate::authorize('delete', $assurance);
        $assurance->delete();

        return redirect()
            ->route('admin.assurances.index')
            ->with('success', 'Assureur archivé.');
    }

    private function sanitize(array $data): array
    {
        $data['tiers_payant'] = (bool) ($data['tiers_payant'] ?? false);
        $data['pec_temps_reel'] = (bool) ($data['pec_temps_reel'] ?? false);
        $data['actif'] = (bool) ($data['actif'] ?? true);
        $data['partenaire'] = (bool) ($data['partenaire'] ?? false);

        return $data;
    }
}
