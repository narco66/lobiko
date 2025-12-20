<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DevisStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'numero_devis' => ['nullable', 'string', 'max:100', 'unique:devis,numero_devis'],
            'patient_id' => ['required', 'uuid', 'exists:users,id'],
            'praticien_id' => ['required', 'uuid', 'exists:users,id'],
            'structure_id' => ['nullable', 'uuid', 'exists:structures_medicales,id'],
            'consultation_id' => ['nullable', 'uuid', 'exists:consultations,id'],
            'rendez_vous_id' => ['nullable', 'uuid', 'exists:rendez_vous,id'],
            'montant_ht' => ['nullable', 'numeric', 'min:0'],
            'montant_tva' => ['nullable', 'numeric', 'min:0'],
            'montant_ttc' => ['nullable', 'numeric', 'min:0'],
            'montant_remise' => ['nullable', 'numeric', 'min:0'],
            'montant_majoration' => ['nullable', 'numeric', 'min:0'],
            'montant_final' => ['required', 'numeric', 'min:0'],
            'montant_assurance' => ['nullable', 'numeric', 'min:0'],
            'reste_a_charge' => ['nullable', 'numeric', 'min:0'],
            'simulation_pec' => ['sometimes', 'boolean'],
            'detail_couverture' => ['nullable', 'array'],
            'date_emission' => ['nullable', 'date'],
            'date_validite' => ['nullable', 'date'],
            'duree_validite' => ['nullable', 'integer', 'min:0'],
            'statut' => ['required', Rule::in(['brouillon','emis','envoye','accepte','refuse','expire','converti'])],
            'notes_internes' => ['nullable', 'string'],
            'conditions_particulieres' => ['nullable', 'string'],
        ];
    }
}
