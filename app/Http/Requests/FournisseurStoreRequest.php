<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FournisseurStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'nom_fournisseur' => ['required', 'string', 'max:255'],
            'numero_licence' => ['required', 'string', 'max:190', 'unique:fournisseurs_pharmaceutiques,numero_licence'],
            'telephone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:190'],
            'adresse' => ['required', 'string'],
            'personne_contact' => ['nullable', 'string', 'max:190'],
            'telephone_contact' => ['nullable', 'string', 'max:50'],
            'categories_produits' => ['nullable', 'array'],
            'delai_livraison_jours' => ['nullable', 'integer', 'min:0'],
            'montant_minimum_commande' => ['nullable', 'numeric', 'min:0'],
            'statut' => ['required', Rule::in(['actif', 'inactif'])],
        ];
    }
}
