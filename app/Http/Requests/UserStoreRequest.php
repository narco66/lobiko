<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserStoreRequest extends FormRequest
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
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'date_naissance' => ['required', 'date'],
            'sexe' => ['required', Rule::in(['M', 'F'])],
            'telephone' => ['required', 'string', 'max:20'],
            'adresse_ville' => ['nullable', 'string', 'max:255'],
            'adresse_pays' => ['nullable', 'string', 'max:255'],
            'statut_compte' => ['nullable', Rule::in(['actif', 'suspendu'])],
            'email_verified_at' => ['nullable', 'date'],
            'roles' => ['array'],
            'roles.*' => ['string'],
        ];
    }
}
