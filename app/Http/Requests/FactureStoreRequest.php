<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FactureStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'type' => ['nullable', Rule::in(['consultation','pharmacie','hospitalisation','analyse','imagerie','autre'])],
            'nature' => ['nullable', Rule::in(['normale','avoir','rectificative'])],
            'numero_facture' => ['nullable', 'string', 'max:100', 'unique:factures,numero_facture'],
            'patient_id' => ['required', 'uuid', 'exists:users,id'],
            'praticien_id' => ['required', 'uuid', 'exists:users,id'],
            'montant_final' => ['required', 'numeric', 'min:0'],
            'date_facture' => ['nullable', 'date'],
            'statut_paiement' => ['required', Rule::in([
                'en_attente',
                'partiel',
                'paye',
                'impaye',
                'annule',
                'rembourse',
                'brouillon',
                'envoyee',
                'partiellement_payee',
                'payee',
                'annulee',
            ])],
            'notes_internes' => ['nullable', 'string'],
        ];
    }
}
