<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDossierMedicalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'uuid', 'exists:users,id'],
            'numero_dossier' => ['required', 'string', 'max:50', 'unique:dossiers_medicaux,numero_dossier'],
            'tension_habituelle_sys' => ['nullable', 'numeric', 'min:0'],
            'tension_habituelle_dia' => ['nullable', 'numeric', 'min:0'],
            'poids_habituel' => ['nullable', 'numeric', 'min:0'],
            'taille_cm' => ['nullable', 'numeric', 'min:0'],
            'imc' => ['nullable', 'numeric', 'min:0'],
            'derniere_consultation' => ['nullable', 'date'],
            'allergies' => ['nullable', 'array'],
            'antecedents' => ['nullable', 'array'],
            'traitements_en_cours' => ['nullable', 'array'],
            'vaccinations' => ['nullable', 'array'],
            'historique_familial' => ['nullable', 'array'],
            'habitudes_vie' => ['nullable', 'array'],
            'notes_privees' => ['nullable', 'string'],
        ];
    }
}
