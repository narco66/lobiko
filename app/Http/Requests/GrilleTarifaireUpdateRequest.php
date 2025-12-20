<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GrilleTarifaireUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'nom_grille' => ['required', 'string', 'max:255'],
            'type_client' => ['required', Rule::in(['public', 'prive', 'assure', 'indigent'])],
            'zone' => ['required', Rule::in(['urbain', 'rural', 'periurbain'])],
            'structure_id' => ['nullable', 'uuid', 'exists:structures_medicales,id'],
            'applicable_a' => ['required', Rule::in(['acte', 'produit', 'tous'])],
            'element_id' => ['nullable', 'uuid'],
            'coefficient_multiplicateur' => ['nullable', 'numeric', 'min:0'],
            'majoration_fixe' => ['nullable', 'numeric', 'min:0'],
            'taux_remise' => ['nullable', 'numeric', 'min:0'],
            'tva_applicable' => ['nullable', 'numeric', 'min:0'],
            'quantite_min' => ['nullable', 'integer', 'min:0'],
            'quantite_max' => ['nullable', 'integer', 'min:0'],
            'montant_min' => ['nullable', 'numeric', 'min:0'],
            'montant_max' => ['nullable', 'numeric', 'min:0'],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
            'actif' => ['sometimes', 'boolean'],
            'priorite' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
