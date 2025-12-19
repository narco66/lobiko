<?php

namespace App\Http\Controllers;

use App\Http\Requests\MedicalStructureStoreRequest;
use App\Http\Requests\MedicalStructureUpdateRequest;
use App\Models\MedicalStructure;
use Illuminate\Support\Facades\Gate;

class MedicalStructureController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', MedicalStructure::class);
        $structures = MedicalStructure::orderBy('nom_structure')->paginate(15);
        return view('medical-structures.index', compact('structures'));
    }

    public function create()
    {
        Gate::authorize('create', MedicalStructure::class);
        return view('medical-structures.create');
    }

    public function store(MedicalStructureStoreRequest $request)
    {
        $data = $request->validated();
        MedicalStructure::create($data);

        return redirect()->route('admin.structures.index')->with('success', 'Structure créée avec succès.');
    }

    public function show(MedicalStructure $structure)
    {
        Gate::authorize('view', $structure);
        return view('medical-structures.show', compact('structure'));
    }

    public function edit(MedicalStructure $structure)
    {
        Gate::authorize('update', $structure);
        return view('medical-structures.edit', compact('structure'));
    }

    public function update(MedicalStructureUpdateRequest $request, MedicalStructure $structure)
    {
        Gate::authorize('update', $structure);
        $structure->update($request->validated());

        return redirect()->route('admin.structures.index')->with('success', 'Structure mise à jour.');
    }

    public function destroy(MedicalStructure $structure)
    {
        Gate::authorize('delete', $structure);
        $structure->delete();

        return redirect()->route('admin.structures.index')->with('success', 'Structure désactivée.');
    }
}
