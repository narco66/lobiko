<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActeMedicalStoreRequest;
use App\Http\Requests\ActeMedicalUpdateRequest;
use App\Models\ActeMedical;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ActeMedicalController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', ActeMedical::class);

        $search = $request->get('search');

        $actes = ActeMedical::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('code_acte', 'like', "%{$search}%")
                        ->orWhere('libelle', 'like', "%{$search}%")
                        ->orWhere('categorie', 'like', "%{$search}%")
                        ->orWhere('specialite', 'like', "%{$search}%");
                });
            })
            ->orderBy('libelle')
            ->paginate(15)
            ->withQueryString();

        return view('actes-medicaux.index', compact('actes'));
    }

    public function create()
    {
        Gate::authorize('create', ActeMedical::class);
        return view('actes-medicaux.create');
    }

    public function store(ActeMedicalStoreRequest $request)
    {
        Gate::authorize('create', ActeMedical::class);

        $acte = ActeMedical::create($this->sanitize($request->validated()));

        return redirect()
            ->route('admin.actes-medicaux.show', $acte)
            ->with('success', 'Acte médical créé avec succès.');
    }

    public function show(ActeMedical $actes_medical)
    {
        Gate::authorize('view', $actes_medical);
        return view('actes-medicaux.show', ['acte' => $actes_medical]);
    }

    public function edit(ActeMedical $actes_medical)
    {
        Gate::authorize('update', $actes_medical);
        return view('actes-medicaux.edit', ['acte' => $actes_medical]);
    }

    public function update(ActeMedicalUpdateRequest $request, ActeMedical $actes_medical)
    {
        Gate::authorize('update', $actes_medical);

        $actes_medical->update($this->sanitize($request->validated()));

        return redirect()
            ->route('admin.actes-medicaux.show', $actes_medical)
            ->with('success', 'Acte médical mis à jour.');
    }

    public function destroy(ActeMedical $actes_medical)
    {
        Gate::authorize('delete', $actes_medical);
        $actes_medical->delete();

        return redirect()
            ->route('admin.actes-medicaux.index')
            ->with('success', 'Acte médical supprimé.');
    }

    private function sanitize(array $data): array
    {
        $data['urgence_possible'] = (bool) ($data['urgence_possible'] ?? false);
        $data['teleconsultation_possible'] = (bool) ($data['teleconsultation_possible'] ?? false);
        $data['domicile_possible'] = (bool) ($data['domicile_possible'] ?? false);
        $data['remboursable'] = (bool) ($data['remboursable'] ?? true);
        $data['actif'] = (bool) ($data['actif'] ?? true);
        return $data;
    }
}
