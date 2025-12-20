<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssuranceStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'code_assureur' => ['required', 'string', 'max:50', 'unique:compagnies_assurance,code_assureur'],
            'nom_assureur' => ['required', 'string', 'max:255'],
            'nom_commercial' => ['nullable', 'string', 'max:255'],
            'type' => ['required', Rule::in(['prive', 'public', 'mutuelle', 'internationale'])],
            'numero_agrement' => ['required', 'string', 'max:190'],
            'numero_fiscal' => ['nullable', 'string', 'max:190'],
            'registre_commerce' => ['nullable', 'string', 'max:190'],
            'adresse' => ['required', 'string', 'max:255'],
            'ville' => ['required', 'string', 'max:150'],
            'pays' => ['required', 'string', 'max:150'],
            'telephone' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:190'],
            'site_web' => ['nullable', 'string', 'max:190'],
            'email_medical' => ['nullable', 'email', 'max:190'],
            'telephone_medical' => ['nullable', 'string', 'max:50'],
            'tiers_payant' => ['sometimes', 'boolean'],
            'pec_temps_reel' => ['sometimes', 'boolean'],
            'delai_remboursement' => ['nullable', 'integer', 'min:0'],
            'actif' => ['sometimes', 'boolean'],
            'partenaire' => ['sometimes', 'boolean'],
            'date_partenariat' => ['nullable', 'date'],
            'fin_partenariat' => ['nullable', 'date'],
        ];
    }
}
