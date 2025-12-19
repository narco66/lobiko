<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MedicalStructureStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('structures.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'code_structure' => ['required', 'string', 'max:50', 'unique:structures_medicales,code_structure'],
            'nom_structure' => ['required', 'string', 'max:255'],
            'type_structure' => ['required', 'in:cabinet,clinique,hopital,pharmacie,laboratoire,centre_imagerie,centre_specialise'],
            'adresse_rue' => ['required', 'string', 'max:255'],
            'adresse_quartier' => ['required', 'string', 'max:255'],
            'adresse_ville' => ['required', 'string', 'max:255'],
            'adresse_pays' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'telephone_principal' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:190'],
            'horaires_ouverture' => ['nullable', 'array'],
            'services_disponibles' => ['nullable', 'array'],
            'equipements' => ['nullable', 'array'],
            'statut' => ['required', 'in:actif,suspendu,ferme,en_validation'],
            'responsable_id' => ['required', 'uuid', 'exists:users,id'],
        ];
    }
}
