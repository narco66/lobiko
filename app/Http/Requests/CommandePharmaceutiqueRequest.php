<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CommandePharmaceutiqueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'pharmacie_id' => ['required', 'uuid', 'exists:pharmacies,id'],
            'ordonnance_id' => ['nullable', 'uuid', 'exists:ordonnances,id'],
            'mode_retrait' => ['required', Rule::in(['sur_place', 'livraison'])],
            'adresse_livraison' => ['required_if:mode_retrait,livraison', 'string', 'max:500'],
            'latitude_livraison' => ['required_if:mode_retrait,livraison', 'numeric'],
            'longitude_livraison' => ['required_if:mode_retrait,livraison', 'numeric'],
            'instructions_speciales' => ['nullable', 'string', 'max:1000'],
            'urgent' => ['nullable', 'boolean'],
            'produits' => ['required', 'array', 'min:1'],
            'produits.*.produit_id' => ['required', 'uuid', 'exists:produits_pharmaceutiques,id'],
            'produits.*.quantite' => ['required', 'integer', 'min:1'],
            'produits.*.taux_remboursement' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'produits.*.posologie' => ['nullable', 'string', 'max:255'],
            'produits.*.duree_traitement' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
