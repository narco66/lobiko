<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDossierMedicalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $dossierId = $this->route('dossier')?->id;

        return [
            'patient_id' => ['sometimes', 'uuid', 'exists:users,id'],
            'numero_dossier' => ['sometimes', 'string', 'max:50', 'unique:dossiers_medicaux,numero_dossier,' . $dossierId],
            'groupe_sanguin' => ['nullable', 'string', 'max:5'],
            'rhesus' => ['nullable', 'string', 'max:5'],
            'allergies' => ['nullable', 'array'],
            'antecedents_medicaux' => ['nullable', 'array'],
            'antecedents_chirurgicaux' => ['nullable', 'array'],
            'antecedents_familiaux' => ['nullable', 'array'],
            'vaccinations' => ['nullable', 'array'],
            'tabac' => ['nullable', 'string', 'max:50'],
            'cigarettes_jour' => ['nullable', 'integer', 'min:0'],
            'alcool' => ['nullable', 'string', 'max:50'],
            'activite_physique' => ['nullable', 'string', 'max:50'],
            'regime_alimentaire' => ['nullable', 'string'],
            'traitements_chroniques' => ['nullable', 'array'],
            'medicaments_actuels' => ['nullable', 'array'],
            'derniere_mise_jour_traitement' => ['nullable', 'date'],
            'date_dernieres_regles' => ['nullable', 'date'],
            'enceinte' => ['nullable', 'boolean'],
            'nombre_grossesses' => ['nullable', 'integer', 'min:0'],
            'nombre_enfants' => ['nullable', 'integer', 'min:0'],
            'contraception' => ['nullable', 'string', 'max:100'],
            'tension_habituelle_sys' => ['nullable', 'numeric', 'min:0'],
            'tension_habituelle_dia' => ['nullable', 'numeric', 'min:0'],
            'poids_habituel' => ['nullable', 'numeric', 'min:0'],
            'taille_cm' => ['nullable', 'numeric', 'min:0'],
            'imc' => ['nullable', 'numeric', 'min:0'],
            'contact_urgence_nom' => ['nullable', 'string', 'max:190'],
            'contact_urgence_telephone' => ['nullable', 'string', 'max:50'],
            'contact_urgence_lien' => ['nullable', 'string', 'max:190'],
            'derniere_consultation' => ['nullable', 'date'],
            'acces_autorises' => ['nullable', 'array'],
            'elements_caches' => ['nullable', 'array'],
            'partage_autorise' => ['nullable', 'boolean'],
            'actif' => ['nullable', 'boolean'],
            'notes_privees' => ['nullable', 'string'],
        ];
    }
}
