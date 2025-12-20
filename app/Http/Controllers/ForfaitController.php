<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForfaitStoreRequest;
use App\Http\Requests\ForfaitUpdateRequest;
use App\Models\Forfait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ForfaitController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Forfait::class);

        $search = $request->get('search');

        $forfaits = Forfait::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('code_forfait', 'like', "%{$search}%")
                        ->orWhere('nom_forfait', 'like', "%{$search}%")
                        ->orWhere('categorie', 'like', "%{$search}%");
                });
            })
            ->orderBy('nom_forfait')
            ->paginate(15)
            ->withQueryString();

        return view('forfaits.index', compact('forfaits'));
    }

    public function create()
    {
        Gate::authorize('create', Forfait::class);
        return view('forfaits.create');
    }

    public function store(ForfaitStoreRequest $request)
    {
        Gate::authorize('create', Forfait::class);

        $forfait = Forfait::create($this->sanitize($request->validated()));

        return redirect()
            ->route('admin.forfaits.show', $forfait)
            ->with('success', 'Forfait créé avec succès.');
    }

    public function show(Forfait $forfait)
    {
        Gate::authorize('view', $forfait);
        return view('forfaits.show', compact('forfait'));
    }

    public function edit(Forfait $forfait)
    {
        Gate::authorize('update', $forfait);
        return view('forfaits.edit', compact('forfait'));
    }

    public function update(ForfaitUpdateRequest $request, Forfait $forfait)
    {
        Gate::authorize('update', $forfait);

        $forfait->update($this->sanitize($request->validated()));

        return redirect()
            ->route('admin.forfaits.show', $forfait)
            ->with('success', 'Forfait mis à jour.');
    }

    public function destroy(Forfait $forfait)
    {
        Gate::authorize('delete', $forfait);
        $forfait->delete();

        return redirect()
            ->route('admin.forfaits.index')
            ->with('success', 'Forfait supprimé.');
    }

    private function sanitize(array $data): array
    {
        $data['remboursable'] = (bool) ($data['remboursable'] ?? true);
        $data['actif'] = (bool) ($data['actif'] ?? true);
        return $data;
    }
}
