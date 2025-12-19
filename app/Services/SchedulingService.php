<?php

namespace App\Services;

use App\Models\DoctorSchedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SchedulingService
{
    public function createSchedule(array $data): DoctorSchedule
    {
        return DB::transaction(function () use ($data) {
            $this->assertNoOverlap($data);
            return DoctorSchedule::create($data);
        });
    }

    protected function assertNoOverlap(array $data): void
    {
        $query = DoctorSchedule::where('doctor_id', $data['doctor_id'])
            ->where(function ($q) use ($data) {
                if (!empty($data['date'])) {
                    $q->where('date', $data['date']);
                } elseif (isset($data['day_of_week'])) {
                    $q->where('day_of_week', $data['day_of_week']);
                }
            })
            ->where(function ($q) use ($data) {
                $q->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                    ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']])
                    ->orWhere(function ($qq) use ($data) {
                        $qq->where('start_time', '<=', $data['start_time'])
                           ->where('end_time', '>=', $data['end_time']);
                    });
            });

        if (!empty($data['structure_id'])) {
            $query->where('structure_id', $data['structure_id']);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'schedule' => 'Chevauchement détecté avec un créneau existant.'
            ]);
        }
    }
}
