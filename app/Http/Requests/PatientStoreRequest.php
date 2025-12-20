<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PatientStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'telephone' => ['required', 'string', 'max:20', 'unique:users,telephone'],
            'date_naissance' => ['required', 'date'],
            'sexe' => ['required', Rule::in(['M', 'F'])],
            'adresse_rue' => ['nullable', 'string', 'max:255'],
            'adresse_quartier' => ['nullable', 'string', 'max:255'],
            'adresse_ville' => ['required', 'string', 'max:255'],
            'adresse_pays' => ['required', 'string', 'max:255'],
            'statut_compte' => ['nullable', Rule::in(['actif', 'suspendu', 'en_attente'])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'notifications_sms' => ['sometimes', 'boolean'],
            'notifications_email' => ['sometimes', 'boolean'],
            'notifications_push' => ['sometimes', 'boolean'],
        ];
    }
}
