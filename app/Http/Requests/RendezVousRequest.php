<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RendezVousRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $rdvId = $this->route('appointment')?->id ?? null;

        return [
            'numero_rdv' => ['nullable', 'string', 'max:100'],
            'patient_id' => ['required', 'uuid', 'exists:users,id'],
            'professionnel_id' => ['required', 'uuid', 'exists:users,id'],
            'structure_id' => ['nullable', 'uuid', 'exists:structures_medicales,id'],
            'date_heure' => ['required', 'date'],
            'duree_prevue' => ['nullable', 'integer', 'min:0'],
            'type' => ['nullable', 'string', 'max:100'],
            'modalite' => ['required', 'in:presentiel,teleconsultation,domicile'],
            'lieu_type' => ['nullable', 'in:cabinet,clinique,domicile,visio'],
            'specialite' => ['nullable', 'string', 'max:190'],
            'motif' => ['nullable', 'string', 'max:500'],
            'symptomes' => ['nullable', 'array'],
            'urgence_niveau' => ['nullable', 'string', 'max:50'],
            'statut' => ['nullable', 'string', 'max:50'],
            'notes_patient' => ['nullable', 'string'],
            'instructions_preparation' => ['nullable', 'string'],
            'documents_requis' => ['nullable', 'array'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'symptomes' => $this->symptomes ?: null,
            'documents_requis' => $this->documents_requis ?: null,
        ]);
    }
}
