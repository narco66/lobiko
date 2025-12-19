<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DoctorUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('doctors.update') ?? false;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['nullable', 'uuid', 'exists:users,id'],
            'matricule' => [
                'required',
                'string',
                'max:100',
                Rule::unique('doctors', 'matricule')->ignore($this->route('doctor'))
            ],
            'nom' => ['required', 'string', 'max:150'],
            'prenom' => ['required', 'string', 'max:150'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:190'],
            'specialty_id' => ['nullable', 'uuid', 'exists:specialties,id'],
            'statut' => ['required', 'in:actif,suspendu,en_validation'],
            'specialties' => ['array'],
            'specialties.*' => ['uuid', 'exists:specialties,id'],
            'structures' => ['array'],
            'structures.*' => ['uuid', 'exists:structures_medicales,id'],
        ];
    }
}
