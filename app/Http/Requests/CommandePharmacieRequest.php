<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommandePharmacieRequest extends FormRequest
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
            'pharmacie_id' => ['required', 'uuid'],
            'mode_livraison' => ['required', 'in:livraison,retrait,domicile,point_relais'],
            'adresse_livraison' => ['nullable', 'string'],
        ];
    }
}
