<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActeMedicalUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $acteId = $this->route('actes-medical')?->id ?? $this->route('actes-medicals')?->id ?? null;

        return [
            'code_acte' => [
                'required',
                'string',
                'max:100',
                Rule::unique('actes_medicaux', 'code_acte')->ignore($acteId),
            ],
            'libelle' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'categorie' => ['required', 'string', 'max:190'],
            'specialite' => ['nullable', 'string', 'max:190'],
            'tarif_base' => ['required', 'numeric', 'min:0'],
            'duree_prevue' => ['nullable', 'integer', 'min:0'],
            'urgence_possible' => ['sometimes', 'boolean'],
            'teleconsultation_possible' => ['sometimes', 'boolean'],
            'domicile_possible' => ['sometimes', 'boolean'],
            'prerequis' => ['nullable', 'array'],
            'contre_indications' => ['nullable', 'array'],
            'age_minimum' => ['nullable', 'integer', 'min:0'],
            'age_maximum' => ['nullable', 'integer', 'min:0'],
            'sexe_requis' => ['required', Rule::in(['M', 'F', 'Tous'])],
            'equipements_requis' => ['nullable', 'array'],
            'consommables' => ['nullable', 'array'],
            'tarif_urgence' => ['nullable', 'numeric', 'min:0'],
            'tarif_weekend' => ['nullable', 'numeric', 'min:0'],
            'tarif_nuit' => ['nullable', 'numeric', 'min:0'],
            'tarif_domicile' => ['nullable', 'numeric', 'min:0'],
            'remboursable' => ['sometimes', 'boolean'],
            'taux_remboursement_base' => ['nullable', 'numeric', 'min:0'],
            'code_securite_sociale' => ['nullable', 'string', 'max:190'],
            'actif' => ['sometimes', 'boolean'],
            'date_debut_validite' => ['nullable', 'date'],
            'date_fin_validite' => ['nullable', 'date', 'after_or_equal:date_debut_validite'],
        ];
    }
}
