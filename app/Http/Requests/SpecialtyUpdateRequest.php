<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SpecialtyUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('specialties.update') ?? false;
    }

    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('specialties', 'code')->ignore($this->route('specialty'))
            ],
            'libelle' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'actif' => ['boolean'],
        ];
    }
}
