<?php

namespace App\Http\Controllers;

use App\Http\Requests\DoctorStoreRequest;
use App\Http\Requests\DoctorUpdateRequest;
use App\Models\Doctor;
use App\Models\MedicalStructure;
use App\Models\Specialty;
use App\Services\DoctorAssignmentService;
use Illuminate\Support\Facades\Gate;

class DoctorController extends Controller
{
    public function __construct(private DoctorAssignmentService $assignmentService)
    {
    }

    public function index()
    {
        Gate::authorize('viewAny', Doctor::class);
        $doctors = Doctor::with(['specialty'])->orderBy('nom')->paginate(15);
        return view('doctors.index', [
            'doctors' => $doctors,
        ]);
    }

    public function create()
    {
        Gate::authorize('create', Doctor::class);
        return view('doctors.create', [
            'specialties' => Specialty::actives()->orderBy('libelle')->get(),
            'structures' => MedicalStructure::actives()->orderBy('nom_structure')->get(),
        ]);
    }

    public function store(DoctorStoreRequest $request)
    {
        $data = $request->validated();
        Gate::authorize('create', Doctor::class);

        $doctor = Doctor::create($data);
        if (!empty($data['specialties'])) {
            $doctor->specialties()->sync($data['specialties']);
        } elseif (!empty($data['specialty_id'])) {
            $doctor->specialties()->sync([$data['specialty_id']]);
        }
        if (!empty($data['structures'])) {
            foreach ($data['structures'] as $structureId) {
                $this->assignmentService->assign($doctor, MedicalStructure::findOrFail($structureId));
            }
        }

        return redirect()->route('admin.doctors.index')->with('success', 'Médecin créé.');
    }

    public function show(Doctor $doctor)
    {
        Gate::authorize('view', $doctor);
        $doctor->load(['user', 'specialties', 'structures']);
        return view('doctors.show', compact('doctor'));
    }

    public function edit(Doctor $doctor)
    {
        Gate::authorize('update', $doctor);
        return view('doctors.edit', [
            'doctor' => $doctor->load(['specialties', 'structures']),
            'specialties' => Specialty::actives()->orderBy('libelle')->get(),
            'structures' => MedicalStructure::actives()->orderBy('nom_structure')->get(),
        ]);
    }

    public function update(DoctorUpdateRequest $request, Doctor $doctor)
    {
        Gate::authorize('update', $doctor);
        $data = $request->validated();
        $doctor->update($data);
        $doctor->specialties()->sync($data['specialties'] ?? ($data['specialty_id'] ? [$data['specialty_id']] : []));
        $doctor->structures()->sync($data['structures'] ?? []);

        return redirect()->route('admin.doctors.index')->with('success', 'Médecin mis à jour.');
    }

    public function destroy(Doctor $doctor)
    {
        Gate::authorize('delete', $doctor);
        $doctor->delete();
        return redirect()->route('admin.doctors.index')->with('success', 'Médecin désactivé.');
    }
}
