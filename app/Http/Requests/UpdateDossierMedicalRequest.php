<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDossierMedicalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $dossierId = $this->route('dossier')?->id;

        return [
            'patient_id' => ['sometimes', 'uuid', 'exists:users,id'],
            'numero_dossier' => ['sometimes', 'string', 'max:50', 'unique:dossiers_medicaux,numero_dossier,' . $dossierId],
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
