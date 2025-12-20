<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ForfaitUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $forfaitId = $this->route('forfait')?->id ?? null;

        return [
            'code_forfait' => [
                'required',
                'string',
                'max:100',
                Rule::unique('forfaits', 'code_forfait')->ignore($forfaitId),
            ],
            'nom_forfait' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'categorie' => ['required', 'string', 'max:190'],
            'prix_forfait' => ['required', 'numeric', 'min:0'],
            'duree_validite' => ['nullable', 'integer', 'min:0'],
            'nombre_seances' => ['nullable', 'integer', 'min:0'],
            'actes_inclus' => ['nullable', 'array'],
            'produits_inclus' => ['nullable', 'array'],
            'examens_inclus' => ['nullable', 'array'],
            'age_minimum' => ['nullable', 'integer', 'min:0'],
            'age_maximum' => ['nullable', 'integer', 'min:0'],
            'sexe_requis' => ['required', Rule::in(['M', 'F', 'Tous'])],
            'pathologies_cibles' => ['nullable', 'array'],
            'remboursable' => ['sometimes', 'boolean'],
            'taux_remboursement' => ['nullable', 'numeric', 'min:0'],
            'actif' => ['sometimes', 'boolean'],
        ];
    }
}
