<?php

namespace App\Http\Controllers;

use App\Http\Requests\SpecialtyStoreRequest;
use App\Http\Requests\SpecialtyUpdateRequest;
use App\Models\Specialty;
use Illuminate\Support\Facades\Gate;

class SpecialtyController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Specialty::class);
        $specialties = Specialty::orderBy('libelle')->paginate(15);
        return view('specialties.index', compact('specialties'));
    }

    public function create()
    {
        Gate::authorize('create', Specialty::class);
        return view('specialties.create');
    }

    public function store(SpecialtyStoreRequest $request)
    {
        Specialty::create($request->validated());
        return redirect()->route('admin.specialties.index')->with('success', 'Spécialité créée.');
    }

    public function edit(Specialty $specialty)
    {
        Gate::authorize('update', $specialty);
        return view('specialties.edit', compact('specialty'));
    }

    public function update(SpecialtyUpdateRequest $request, Specialty $specialty)
    {
        Gate::authorize('update', $specialty);
        $specialty->update($request->validated());
        return redirect()->route('admin.specialties.index')->with('success', 'Spécialité mise à jour.');
    }

    public function destroy(Specialty $specialty)
    {
        Gate::authorize('delete', $specialty);
        $specialty->delete();
        return redirect()->route('admin.specialties.index')->with('success', 'Spécialité supprimée.');
    }
}
