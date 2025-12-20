<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProduitPharmaceutiqueStoreRequest;
use App\Http\Requests\ProduitPharmaceutiqueUpdateRequest;
use App\Models\ProduitPharmaceutique;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProduitPharmaceutiqueController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', ProduitPharmaceutique::class);

        $search = $request->get('search');

        $produits = ProduitPharmaceutique::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('code_produit', 'like', "%{$search}%")
                        ->orWhere('dci', 'like', "%{$search}%")
                        ->orWhere('nom_commercial', 'like', "%{$search}%")
                        ->orWhere('classe_therapeutique', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('disponible'), fn($q) => $q->where('disponible', (bool) $request->disponible))
            ->when($request->filled('generique'), fn($q) => $q->where('generique', (bool) $request->generique))
            ->orderBy('nom_commercial')
            ->paginate(15)
            ->withQueryString();

        return view('produits-pharmaceutiques.index', compact('produits'));
    }

    public function create()
    {
        Gate::authorize('create', ProduitPharmaceutique::class);
        return view('produits-pharmaceutiques.create');
    }

    public function store(ProduitPharmaceutiqueStoreRequest $request)
    {
        Gate::authorize('create', ProduitPharmaceutique::class);

        $data = $this->sanitize($request->validated());
        $produit = ProduitPharmaceutique::create($data);

        return redirect()
            ->route('admin.produits-pharmaceutiques.show', $produit)
            ->with('success', 'Produit créé avec succès.');
    }

    public function show(ProduitPharmaceutique $produits_pharmaceutique)
    {
        Gate::authorize('view', $produits_pharmaceutique);
        return view('produits-pharmaceutiques.show', ['produit' => $produits_pharmaceutique]);
    }

    public function edit(ProduitPharmaceutique $produits_pharmaceutique)
    {
        Gate::authorize('update', $produits_pharmaceutique);
        return view('produits-pharmaceutiques.edit', ['produit' => $produits_pharmaceutique]);
    }

    public function update(ProduitPharmaceutiqueUpdateRequest $request, ProduitPharmaceutique $produits_pharmaceutique)
    {
        Gate::authorize('update', $produits_pharmaceutique);

        $data = $this->sanitize($request->validated());
        $produits_pharmaceutique->update($data);

        return redirect()
            ->route('admin.produits-pharmaceutiques.show', $produits_pharmaceutique)
            ->with('success', 'Produit mis à jour.');
    }

    public function destroy(ProduitPharmaceutique $produits_pharmaceutique)
    {
        Gate::authorize('delete', $produits_pharmaceutique);
        $produits_pharmaceutique->delete();

        return redirect()
            ->route('admin.produits-pharmaceutiques.index')
            ->with('success', 'Produit supprimé.');
    }

    private function sanitize(array $data): array
    {
        $data['generique'] = (bool) ($data['generique'] ?? false);
        $data['prescription_obligatoire'] = (bool) ($data['prescription_obligatoire'] ?? true);
        $data['stupefiant'] = (bool) ($data['stupefiant'] ?? false);
        $data['liste_i'] = (bool) ($data['liste_i'] ?? false);
        $data['liste_ii'] = (bool) ($data['liste_ii'] ?? false);
        $data['remboursable'] = (bool) ($data['remboursable'] ?? true);
        $data['disponible'] = (bool) ($data['disponible'] ?? true);
        $data['rupture_stock'] = (bool) ($data['rupture_stock'] ?? false);

        return $data;
    }
}
