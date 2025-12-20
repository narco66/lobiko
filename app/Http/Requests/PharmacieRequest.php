<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PharmacieRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $pharmacieId = $this->route('pharmacie')?->id ?? null;

        return [
            'structure_medicale_id' => ['required', 'uuid', 'exists:structures_medicales,id'],
            'numero_licence' => [
                'required',
                'string',
                'max:190',
                Rule::unique('pharmacies', 'numero_licence')->ignore($pharmacieId),
            ],
            'nom_pharmacie' => ['required', 'string', 'max:255'],
            'nom_responsable' => ['required', 'string', 'max:255'],
            'telephone_pharmacie' => ['required', 'string', 'max:50'],
            'email_pharmacie' => ['nullable', 'email', 'max:190'],
            'adresse_complete' => ['required', 'string'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'service_garde' => ['sometimes', 'boolean'],
            'livraison_disponible' => ['sometimes', 'boolean'],
            'rayon_livraison_km' => ['nullable', 'numeric', 'min:0'],
            'frais_livraison_base' => ['nullable', 'numeric', 'min:0'],
            'frais_livraison_par_km' => ['nullable', 'numeric', 'min:0'],
            'paiement_mobile_money' => ['sometimes', 'boolean'],
            'paiement_carte' => ['sometimes', 'boolean'],
            'paiement_especes' => ['sometimes', 'boolean'],
            'statut' => ['required', Rule::in(['active', 'inactive', 'suspendue'])],
        ];
    }
}
