<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConsultationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'uuid', 'exists:users,id'],
            'structure_id' => ['required', 'uuid', 'exists:structures_medicales,id'],
            'rendez_vous_id' => ['nullable', 'uuid', 'exists:rendez_vous,id'],
            'date_consultation' => ['required', 'date'],
            'type' => ['required', 'in:initial,controle,urgence,suivi'],
            'modalite' => ['required', 'in:presentiel,teleconsultation'],
            'motif_consultation' => ['required', 'string', 'max:500'],
            'histoire_maladie' => ['nullable', 'string'],
            'diagnostic_principal' => ['nullable', 'string', 'max:500'],
            'code_cim10' => ['nullable', 'string', 'max:50'],
            'conduite_a_tenir' => ['nullable', 'string'],
            'recommandations' => ['nullable', 'string'],
            'symptomes_declares' => ['nullable', 'array'],
            'signes_vitaux' => ['nullable', 'array'],
            'actes_realises' => ['nullable', 'array'],
            'actes_realises.*.id' => ['required_with:actes_realises', 'uuid', 'exists:actes_medicaux,id'],
            'actes_realises.*.quantite' => ['nullable', 'integer', 'min:1'],
            'prescriptions' => ['nullable', 'array'],
            'prescriptions.*.produit_pharmaceutique_id' => ['required_with:prescriptions', 'uuid', 'exists:produits_pharmaceutiques,id'],
            'prescriptions.*.posologie' => ['nullable', 'string', 'max:255'],
            'examens_prescrits' => ['nullable', 'array'],
            'examens_prescrits.*.code' => ['required_with:examens_prescrits', 'string', 'max:50'],
            'examens_prescrits.*.quantite' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
