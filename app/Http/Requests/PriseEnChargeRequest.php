<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\ContratAssurance;

class PriseEnChargeRequest extends FormRequest
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
        $rules = [
            'contrat_id' => 'required|exists:contrats_assurance,id',
            'devis_id' => 'nullable|exists:devis,id',
            'facture_id' => 'nullable|exists:factures,id',
            'praticien_id' => 'nullable|exists:users,id',
            'structure_id' => 'nullable|exists:structures_medicales,id',
            'type_pec' => ['required', Rule::in([
                'consultation',
                'hospitalisation',
                'chirurgie',
                'imagerie',
                'analyses',
                'pharmacie',
                'soins_dentaires',
                'optique',
                'maternite',
                'urgence'
            ])],
            'montant_demande' => 'required|numeric|min:0|max:100000000',
            'motif' => 'required|string|max:500',
            'validite_jours' => 'nullable|integer|min:1|max:365',
            'justificatifs' => 'nullable|array',
            'justificatifs.*' => 'file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ];

        // Si c'est une urgence, validité limitée
        if ($this->input('type_pec') === 'urgence') {
            $rules['validite_jours'] = 'nullable|integer|min:1|max:7';
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'contrat_id' => 'contrat d\'assurance',
            'devis_id' => 'devis',
            'facture_id' => 'facture',
            'praticien_id' => 'praticien',
            'structure_id' => 'structure médicale',
            'type_pec' => 'type de prise en charge',
            'montant_demande' => 'montant demandé',
            'motif' => 'motif',
            'validite_jours' => 'durée de validité',
            'justificatifs' => 'justificatifs',
            'justificatifs.*' => 'fichier justificatif',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'contrat_id.required' => 'Le contrat d\'assurance est obligatoire.',
            'contrat_id.exists' => 'Le contrat d\'assurance sélectionné n\'existe pas.',
            'type_pec.required' => 'Le type de prise en charge est obligatoire.',
            'type_pec.in' => 'Le type de prise en charge sélectionné n\'est pas valide.',
            'montant_demande.required' => 'Le montant demandé est obligatoire.',
            'montant_demande.numeric' => 'Le montant demandé doit être un nombre.',
            'montant_demande.min' => 'Le montant demandé doit être positif.',
            'montant_demande.max' => 'Le montant demandé ne peut pas dépasser 100 000 000 FCFA.',
            'motif.required' => 'Le motif de la demande est obligatoire.',
            'motif.max' => 'Le motif ne doit pas dépasser 500 caractères.',
            'justificatifs.*.mimes' => 'Les justificatifs doivent être au format PDF, JPG ou PNG.',
            'justificatifs.*.max' => 'Chaque justificatif ne doit pas dépasser 5 MB.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Si un devis est lié, récupérer le montant depuis le devis
        if ($this->has('devis_id') && $this->input('devis_id')) {
            $devis = \App\Models\Devis::find($this->input('devis_id'));
            if ($devis) {
                $this->merge([
                    'montant_demande' => $devis->montant_total,
                    'patient_id' => $devis->patient_id,
                ]);
            }
        }

        // Si une facture est liée, récupérer le montant depuis la facture
        if ($this->has('facture_id') && $this->input('facture_id')) {
            $facture = \App\Models\Facture::find($this->input('facture_id'));
            if ($facture) {
                $this->merge([
                    'montant_demande' => $facture->montant_total,
                    'patient_id' => $facture->patient_id,
                ]);
            }
        }

        // Valeurs par défaut
        if (!$this->has('validite_jours')) {
            $this->merge(['validite_jours' => 30]);
        }
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Vérifier que le contrat est valide
            $this->verifierValiditeContrat($validator);

            // Vérifier le plafond disponible
            $this->verifierPlafondDisponible($validator);

            // Vérifier les exclusions
            $this->verifierExclusions($validator);

            // Vérifier qu'il n'y a pas déjà une PEC en cours pour le même dossier
            $this->verifierPecExistante($validator);
        });
    }

    /**
     * Vérifier la validité du contrat
     */
    private function verifierValiditeContrat($validator): void
    {
        if ($this->has('contrat_id')) {
            $contrat = ContratAssurance::find($this->input('contrat_id'));

            if ($contrat && !$contrat->estValide()) {
                $validator->errors()->add(
                    'contrat_id',
                    'Le contrat d\'assurance n\'est pas valide (expiré ou suspendu)'
                );
            }
        }
    }

    /**
     * Vérifier le plafond disponible
     */
    private function verifierPlafondDisponible($validator): void
    {
        if ($this->has('contrat_id') && $this->has('montant_demande')) {
            $contrat = ContratAssurance::find($this->input('contrat_id'));

            if ($contrat) {
                $montantCouvert = $this->input('montant_demande') * ($contrat->taux_couverture / 100);

                if (!$contrat->peutCouvrir($montantCouvert)) {
                    $plafondRestant = $contrat->montantRestant();
                    $validator->errors()->add(
                        'montant_demande',
                        "Le plafond disponible est insuffisant (Restant: " . number_format($plafondRestant, 0, ',', ' ') . " FCFA)"
                    );
                }
            }
        }
    }

    /**
     * Vérifier les exclusions du contrat
     */
    private function verifierExclusions($validator): void
    {
        if ($this->has('contrat_id') && $this->has('type_pec')) {
            $contrat = ContratAssurance::find($this->input('contrat_id'));

            if ($contrat && $contrat->exclusions) {
                // Mapping des types de PEC aux exclusions
                $mappingExclusions = [
                    'chirurgie' => 'chirurgie_esthetique',
                    'soins_dentaires' => 'implants_dentaires',
                    'optique' => 'lunettes_solaires',
                ];

                $typePec = $this->input('type_pec');

                if (isset($mappingExclusions[$typePec])) {
                    $exclusion = $mappingExclusions[$typePec];

                    if (in_array($exclusion, $contrat->exclusions)) {
                        $validator->errors()->add(
                            'type_pec',
                            "Ce type de prise en charge est exclu du contrat"
                        );
                    }
                }
            }
        }
    }

    /**
     * Vérifier qu'il n'y a pas déjà une PEC en cours
     */
    private function verifierPecExistante($validator): void
    {
        // Pour un devis
        if ($this->has('devis_id') && $this->input('devis_id')) {
            $pecExistante = \App\Models\PriseEnCharge::where('devis_id', $this->input('devis_id'))
                ->whereIn('statut', ['en_attente', 'acceptee'])
                ->exists();

            if ($pecExistante) {
                $validator->errors()->add(
                    'devis_id',
                    'Une prise en charge existe déjà pour ce devis'
                );
            }
        }

        // Pour une facture
        if ($this->has('facture_id') && $this->input('facture_id')) {
            $pecExistante = \App\Models\PriseEnCharge::where('facture_id', $this->input('facture_id'))
                ->whereIn('statut', ['en_attente', 'acceptee', 'utilisee'])
                ->exists();

            if ($pecExistante) {
                $validator->errors()->add(
                    'facture_id',
                    'Une prise en charge existe déjà pour cette facture'
                );
            }
        }
    }

    /**
     * Handle a passed validation attempt.
     *
     * @return void
     */
    protected function passedValidation(): void
    {
        // Traiter les fichiers uploadés
        if ($this->hasFile('justificatifs')) {
            $justificatifs = [];

            foreach ($this->file('justificatifs') as $file) {
                $path = $file->store('pec/justificatifs', 'public');
                $justificatifs[] = [
                    'nom' => $file->getClientOriginalName(),
                    'path' => $path,
                    'taille' => $file->getSize(),
                    'type' => $file->getClientMimeType(),
                    'date_upload' => now(),
                ];
            }

            $this->merge(['justificatifs' => $justificatifs]);
        }
    }
}
