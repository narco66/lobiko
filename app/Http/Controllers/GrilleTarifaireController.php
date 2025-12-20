<?php

namespace App\Http\Controllers;

use App\Http\Requests\GrilleTarifaireStoreRequest;
use App\Http\Requests\GrilleTarifaireUpdateRequest;
use App\Models\GrilleTarifaire;
use App\Models\StructureMedicale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class GrilleTarifaireController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', GrilleTarifaire::class);

        $search = $request->get('search');

        $grilles = GrilleTarifaire::with('structure')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nom_grille', 'like', "%{$search}%")
                        ->orWhere('code_forfait', 'like', "%{$search}%")
                        ->orWhere('type_client', 'like', "%{$search}%")
                        ->orWhere('zone', 'like', "%{$search}%");
                });
            })
            ->orderBy('nom_grille')
            ->paginate(15)
            ->withQueryString();

        return view('grilles-tarifaires.index', compact('grilles'));
    }

    public function create()
    {
        Gate::authorize('create', GrilleTarifaire::class);
        $structures = StructureMedicale::orderBy('nom_structure')->get();
        return view('grilles-tarifaires.create', compact('structures'));
    }

    public function store(GrilleTarifaireStoreRequest $request)
    {
        Gate::authorize('create', GrilleTarifaire::class);
        $grille = GrilleTarifaire::create($this->sanitize($request->validated()));

        return redirect()
            ->route('admin.grilles-tarifaires.show', $grille)
            ->with('success', 'Grille tarifaire créée avec succès.');
    }

    public function show(GrilleTarifaire $grilles_tarifaire)
    {
        Gate::authorize('view', $grilles_tarifaire);
        $grilles_tarifaire->load('structure');
        return view('grilles-tarifaires.show', ['grille' => $grilles_tarifaire]);
    }

    public function edit(GrilleTarifaire $grilles_tarifaire)
    {
        Gate::authorize('update', $grilles_tarifaire);
        $structures = StructureMedicale::orderBy('nom_structure')->get();
        return view('grilles-tarifaires.edit', ['grille' => $grilles_tarifaire, 'structures' => $structures]);
    }

    public function update(GrilleTarifaireUpdateRequest $request, GrilleTarifaire $grilles_tarifaire)
    {
        Gate::authorize('update', $grilles_tarifaire);
        $grilles_tarifaire->update($this->sanitize($request->validated()));

        return redirect()
            ->route('admin.grilles-tarifaires.show', $grilles_tarifaire)
            ->with('success', 'Grille tarifaire mise à jour.');
    }

    public function destroy(GrilleTarifaire $grilles_tarifaire)
    {
        Gate::authorize('delete', $grilles_tarifaire);
        $grilles_tarifaire->delete();

        return redirect()
            ->route('admin.grilles-tarifaires.index')
            ->with('success', 'Grille tarifaire supprimée.');
    }

    private function sanitize(array $data): array
    {
        $data['actif'] = (bool) ($data['actif'] ?? true);
        return $data;
    }
}
