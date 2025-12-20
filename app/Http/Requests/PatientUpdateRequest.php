<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PatientUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $patientId = $this->route('patient')?->id ?? null;

        return [
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($patientId)],
            'telephone' => ['required', 'string', 'max:20', Rule::unique('users', 'telephone')->ignore($patientId)],
            'date_naissance' => ['required', 'date'],
            'sexe' => ['required', Rule::in(['M', 'F'])],
            'adresse_rue' => ['nullable', 'string', 'max:255'],
            'adresse_quartier' => ['nullable', 'string', 'max:255'],
            'adresse_ville' => ['required', 'string', 'max:255'],
            'adresse_pays' => ['required', 'string', 'max:255'],
            'statut_compte' => ['nullable', Rule::in(['actif', 'suspendu', 'en_attente'])],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'notifications_sms' => ['sometimes', 'boolean'],
            'notifications_email' => ['sometimes', 'boolean'],
            'notifications_push' => ['sometimes', 'boolean'],
        ];
    }
}
