<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaiementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'facture_id' => ['nullable', 'uuid', 'exists:factures,id'],
            'payeur_id' => ['nullable', 'uuid', 'exists:users,id'],
            'commande_id' => [
                'nullable',
                'uuid',
                'exists:commandes_pharmaceutiques,id',
                Rule::requiredIf(fn () => !$this->facture_id && ($this->type_reference === 'commande_pharmaceutique')),
            ],
            'reference_id' => ['nullable', 'uuid'],
            'type_reference' => ['nullable', 'string', 'max:100'],
            'type_payeur' => ['required', 'in:patient,assurance,subvention'],
            'mode_paiement' => ['required', 'in:especes,carte_bancaire,virement,cheque,mobile_money_airtel,mobile_money_mtn,mobile_money_orange,mobile_money_moov,paypal,voucher'],
            'montant' => ['required', 'numeric', 'min:0.01'],
            'devise' => ['nullable', 'string', 'size:3'],
            'taux_change' => ['nullable', 'numeric', 'min:0'],
            'montant_devise_locale' => ['nullable', 'numeric', 'min:0'],
            'frais_transaction' => ['nullable', 'numeric', 'min:0'],
            'montant_net' => ['nullable', 'numeric', 'min:0'],
            'reference_transaction' => ['nullable', 'string', 'max:255', 'unique:paiements,reference_transaction'],
            'reference_passerelle' => ['nullable', 'string', 'max:255'],
            'idempotence_key' => ['nullable', 'string', 'max:255', 'unique:paiements,idempotence_key'],
            'passerelle' => ['nullable', 'string', 'max:100'],
            'reponse_passerelle' => ['nullable', 'array'],
            'code_autorisation' => ['nullable', 'string', 'max:100'],
            'code_erreur' => ['nullable', 'string', 'max:100'],
            'message_erreur' => ['nullable', 'string'],
            'remboursable' => ['nullable', 'boolean'],
            'montant_rembourse' => ['nullable', 'numeric', 'min:0'],
            'reference_remboursement' => ['nullable', 'string', 'max:255'],
            'valide_par' => ['nullable', 'uuid', 'exists:users,id'],
            'agent_id' => ['nullable', 'uuid', 'exists:users,id'],
            'code_agent' => ['nullable', 'string', 'max:50'],
            'lieu_paiement' => ['nullable', 'string', 'max:255'],
            'recu_pdf' => ['nullable', 'string', 'max:255'],
            'preuve_paiement' => ['nullable', 'string', 'max:255'],
        ];
    }
}
