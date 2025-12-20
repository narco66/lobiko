<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProduitPharmaceutiqueUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $produitId = $this->route('produits-pharmaceutique')?->id ?? $this->route('produits-pharmaceutiques')?->id ?? null;

        return [
            'code_produit' => [
                'required',
                'string',
                'max:100',
                Rule::unique('produits_pharmaceutiques', 'code_produit')->ignore($produitId),
            ],
            'dci' => ['required', 'string', 'max:255'],
            'nom_commercial' => ['required', 'string', 'max:255'],
            'laboratoire' => ['nullable', 'string', 'max:255'],
            'forme' => ['nullable', 'string', 'max:190'],
            'dosage' => ['nullable', 'string', 'max:190'],
            'conditionnement' => ['nullable', 'string', 'max:190'],
            'voie_administration' => ['nullable', 'string', 'max:190'],
            'classe_therapeutique' => ['nullable', 'string', 'max:190'],
            'famille' => ['nullable', 'string', 'max:190'],
            'generique' => ['sometimes', 'boolean'],
            'princeps' => ['nullable', 'string', 'max:190'],
            'prix_unitaire' => ['required', 'numeric', 'min:0'],
            'prix_boite' => ['required', 'numeric', 'min:0'],
            'stock_minimum' => ['nullable', 'integer', 'min:0'],
            'stock_alerte' => ['nullable', 'integer', 'min:0'],
            'prescription_obligatoire' => ['sometimes', 'boolean'],
            'stupefiant' => ['sometimes', 'boolean'],
            'liste_i' => ['sometimes', 'boolean'],
            'liste_ii' => ['sometimes', 'boolean'],
            'duree_traitement_max' => ['nullable', 'integer', 'min:0'],
            'conditions_conservation' => ['nullable', 'string'],
            'temperature_min' => ['nullable', 'integer'],
            'temperature_max' => ['nullable', 'integer'],
            'remboursable' => ['sometimes', 'boolean'],
            'taux_remboursement' => ['nullable', 'numeric', 'min:0'],
            'code_cip' => ['nullable', 'string', 'max:190'],
            'code_ucd' => ['nullable', 'string', 'max:190'],
            'disponible' => ['sometimes', 'boolean'],
            'rupture_stock' => ['sometimes', 'boolean'],
        ];
    }
}
