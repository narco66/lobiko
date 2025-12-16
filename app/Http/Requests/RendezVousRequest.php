<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RendezVousRequest extends FormRequest
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
            'professionnel_id' => ['required', 'uuid', 'exists:users,id'],
            'structure_id' => ['required', 'uuid', 'exists:structures_medicales,id'],
            'date_heure' => ['required', 'date', 'after:now'],
            'type' => ['required', 'in:consultation,controle,urgence,teleconsultation'],
            'modalite' => ['required', 'in:presentiel,teleconsultation'],
            'motif' => ['required', 'string', 'max:500'],
            'specialite' => ['nullable', 'string', 'max:255'],
        ];
    }
}
