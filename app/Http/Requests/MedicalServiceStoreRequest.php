<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MedicalServiceStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('services.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:50', 'unique:medical_services,code'],
            'libelle' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'actif' => ['boolean'],
        ];
    }
}
