<?php

namespace App\Http\Controllers;

use App\Http\Requests\MedicalServiceStoreRequest;
use App\Http\Requests\MedicalServiceUpdateRequest;
use App\Models\MedicalService;
use Illuminate\Support\Facades\Gate;

class MedicalServiceController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', MedicalService::class);
        $services = MedicalService::orderBy('libelle')->paginate(15);
        return view('medical-services.index', compact('services'));
    }

    public function create()
    {
        Gate::authorize('create', MedicalService::class);
        return view('medical-services.create');
    }

    public function store(MedicalServiceStoreRequest $request)
    {
        MedicalService::create($request->validated());
        return redirect()->route('admin.services.index')->with('success', 'Service créé.');
    }

    public function edit(MedicalService $service)
    {
        Gate::authorize('update', $service);
        return view('medical-services.edit', compact('service'));
    }

    public function show(MedicalService $service)
    {
        Gate::authorize('view', $service);
        return view('medical-services.show', compact('service'));
    }

    public function update(MedicalServiceUpdateRequest $request, MedicalService $service)
    {
        Gate::authorize('update', $service);
        $service->update($request->validated());
        return redirect()->route('admin.services.index')->with('success', 'Service mis à jour.');
    }

    public function destroy(MedicalService $service)
    {
        Gate::authorize('delete', $service);
        $service->delete();
        return redirect()->route('admin.services.index')->with('success', 'Service supprimé.');
    }
}
