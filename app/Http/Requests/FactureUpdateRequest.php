<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FactureUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $factureId = $this->route('facture')?->id ?? null;

        return [
            'type' => ['nullable', Rule::in(['consultation','pharmacie','hospitalisation','analyse','imagerie','autre'])],
            'nature' => ['nullable', Rule::in(['normale','avoir','rectificative'])],
            'numero_facture' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('factures', 'numero_facture')->ignore($factureId),
            ],
            'patient_id' => ['required', 'uuid', 'exists:users,id'],
            'praticien_id' => ['required', 'uuid', 'exists:users,id'],
            'montant_final' => ['required', 'numeric', 'min:0'],
            'date_facture' => ['nullable', 'date'],
            'statut_paiement' => [
                'required',
                Rule::in([
                    'brouillon',
                    'en_attente',
                    'envoyee',
                    'partiellement_payee',
                    'payee',
                    'paye',
                    'annulee',
                    'annule',
                    'impaye',
                    'partiel',
                    'rembourse',
                ]),
            ],
            'notes_internes' => ['nullable', 'string'],
        ];
    }
}
