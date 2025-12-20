<?php

namespace App\Http\Controllers;

use App\Http\Requests\FournisseurStoreRequest;
use App\Http\Requests\FournisseurUpdateRequest;
use App\Models\FournisseurPharmaceutique;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class FournisseurPharmaceutiqueController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', FournisseurPharmaceutique::class);

        $search = $request->get('search');

        $fournisseurs = FournisseurPharmaceutique::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nom_fournisseur', 'like', "%{$search}%")
                        ->orWhere('numero_licence', 'like', "%{$search}%")
                        ->orWhere('telephone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('nom_fournisseur')
            ->paginate(15)
            ->withQueryString();

        return view('fournisseurs-pharmaceutiques.index', compact('fournisseurs'));
    }

    public function create()
    {
        Gate::authorize('create', FournisseurPharmaceutique::class);
        return view('fournisseurs-pharmaceutiques.create');
    }

    public function store(FournisseurStoreRequest $request)
    {
        Gate::authorize('create', FournisseurPharmaceutique::class);
        $fournisseur = FournisseurPharmaceutique::create($this->sanitize($request->validated()));

        return redirect()
            ->route('admin.fournisseurs-pharmaceutiques.show', $fournisseur)
            ->with('success', 'Fournisseur créé avec succès.');
    }

    public function show(FournisseurPharmaceutique $fournisseurs_pharmaceutique)
    {
        Gate::authorize('view', $fournisseurs_pharmaceutique);
        $fournisseurs_pharmaceutique->load('pharmacies');
        return view('fournisseurs-pharmaceutiques.show', ['fournisseur' => $fournisseurs_pharmaceutique]);
    }

    public function edit(FournisseurPharmaceutique $fournisseurs_pharmaceutique)
    {
        Gate::authorize('update', $fournisseurs_pharmaceutique);
        return view('fournisseurs-pharmaceutiques.edit', ['fournisseur' => $fournisseurs_pharmaceutique]);
    }

    public function update(FournisseurUpdateRequest $request, FournisseurPharmaceutique $fournisseurs_pharmaceutique)
    {
        Gate::authorize('update', $fournisseurs_pharmaceutique);
        $fournisseurs_pharmaceutique->update($this->sanitize($request->validated()));

        return redirect()
            ->route('admin.fournisseurs-pharmaceutiques.show', $fournisseurs_pharmaceutique)
            ->with('success', 'Fournisseur mis à jour.');
    }

    public function destroy(FournisseurPharmaceutique $fournisseurs_pharmaceutique)
    {
        Gate::authorize('delete', $fournisseurs_pharmaceutique);
        $fournisseurs_pharmaceutique->delete();

        return redirect()
            ->route('admin.fournisseurs-pharmaceutiques.index')
            ->with('success', 'Fournisseur supprimé.');
    }

    private function sanitize(array $data): array
    {
        $data['categories_produits'] = $data['categories_produits'] ?? [];
        return $data;
    }
}
