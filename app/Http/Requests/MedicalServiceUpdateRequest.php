<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MedicalServiceUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('services.update') ?? false;
    }

    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('medical_services', 'code')->ignore($this->route('service'))
            ],
            'libelle' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'actif' => ['boolean'],
        ];
    }
}
