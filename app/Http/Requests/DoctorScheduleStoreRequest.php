<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DoctorScheduleStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('schedules.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'doctor_id' => ['required', 'uuid', 'exists:doctors,id'],
            'structure_id' => ['nullable', 'uuid', 'exists:structures_medicales,id'],
            'day_of_week' => ['nullable', 'integer', 'between:0,6'],
            'date' => ['nullable', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'is_recurring' => ['boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ];
    }
}
