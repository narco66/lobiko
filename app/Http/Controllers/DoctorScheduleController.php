<?php

namespace App\Http\Controllers;

use App\Http\Requests\DoctorScheduleStoreRequest;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Services\SchedulingService;
use Illuminate\Support\Facades\Gate;

class DoctorScheduleController extends Controller
{
    public function __construct(private SchedulingService $schedulingService)
    {
    }

    public function store(DoctorScheduleStoreRequest $request)
    {
        Gate::authorize('create', DoctorSchedule::class);
        $schedule = $this->schedulingService->createSchedule($request->validated());

        return redirect()->route('admin.doctors.show', $schedule->doctor_id)
            ->with('success', 'Créneau ajouté.');
    }

    public function destroy(DoctorSchedule $doctorSchedule)
    {
        Gate::authorize('delete', $doctorSchedule);
        $doctorSchedule->delete();

        return back()->with('success', 'Créneau supprimé.');
    }
}
