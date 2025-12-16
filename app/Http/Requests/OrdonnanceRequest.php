<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrdonnanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // L'autorisation est gérée dans le contrôleur via les policies
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
            'consultation_id' => 'nullable|exists:consultations,id',
            'patient_id' => 'required|exists:users,id',
            'diagnostic' => 'required|string|max:500',
            'observations' => 'nullable|string|max:1000',
            'type_ordonnance' => ['required', Rule::in(['normale', 'secure', 'exception', 'hospitaliere'])],
            'validite_jours' => 'nullable|integer|min:1|max:365',
            'renouvelable' => 'boolean',
            'nombre_renouvellements' => 'nullable|integer|min:1|max:12',

            // Validation des lignes de médicaments
            'lignes' => 'required|array|min:1',
            'lignes.*.produit_id' => 'required|exists:produits_pharmaceutiques,id',
            'lignes.*.quantite' => 'required|integer|min:1|max:999',
            'lignes.*.posologie' => 'required|string|max:500',
            'lignes.*.duree_traitement' => 'nullable|integer|min:1|max:365',
            'lignes.*.unite_duree' => ['nullable', Rule::in(['jours', 'semaines', 'mois'])],
            'lignes.*.voie_administration' => ['nullable', Rule::in([
                'Orale', 'Injectable', 'Cutanée', 'Ophtalmique',
                'Nasale', 'Rectale', 'Inhalation', 'Sublinguale'
            ])],
            'lignes.*.instructions_speciales' => 'nullable|string|max:500',
            'lignes.*.substitution_autorisee' => 'boolean',
            'lignes.*.urgence' => 'boolean',
        ];

        // Règles spécifiques pour les ordonnances sécurisées
        if ($this->input('type_ordonnance') === 'secure') {
            $rules['validite_jours'] = 'required|integer|min:1|max:7'; // Max 7 jours pour les stupéfiants
            $rules['renouvelable'] = 'in:0,false'; // Non renouvelable
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
            'patient_id' => 'patient',
            'consultation_id' => 'consultation',
            'diagnostic' => 'diagnostic',
            'observations' => 'observations',
            'type_ordonnance' => 'type d\'ordonnance',
            'validite_jours' => 'durée de validité',
            'renouvelable' => 'renouvelable',
            'nombre_renouvellements' => 'nombre de renouvellements',
            'lignes' => 'médicaments',
            'lignes.*.produit_id' => 'médicament',
            'lignes.*.quantite' => 'quantité',
            'lignes.*.posologie' => 'posologie',
            'lignes.*.duree_traitement' => 'durée du traitement',
            'lignes.*.unite_duree' => 'unité de durée',
            'lignes.*.voie_administration' => 'voie d\'administration',
            'lignes.*.instructions_speciales' => 'instructions spéciales',
            'lignes.*.substitution_autorisee' => 'substitution autorisée',
            'lignes.*.urgence' => 'urgence',
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
            'patient_id.required' => 'Veuillez sélectionner un patient.',
            'patient_id.exists' => 'Le patient sélectionné n\'existe pas.',
            'diagnostic.required' => 'Le diagnostic est obligatoire.',
            'diagnostic.max' => 'Le diagnostic ne doit pas dépasser 500 caractères.',
            'type_ordonnance.required' => 'Le type d\'ordonnance est obligatoire.',
            'type_ordonnance.in' => 'Le type d\'ordonnance sélectionné n\'est pas valide.',
            'validite_jours.min' => 'La durée de validité doit être d\'au moins 1 jour.',
            'validite_jours.max' => 'La durée de validité ne peut pas dépasser :max jours.',
            'lignes.required' => 'Veuillez ajouter au moins un médicament.',
            'lignes.min' => 'L\'ordonnance doit contenir au moins un médicament.',
            'lignes.*.produit_id.required' => 'Veuillez sélectionner un médicament.',
            'lignes.*.produit_id.exists' => 'Le médicament sélectionné n\'existe pas.',
            'lignes.*.quantite.required' => 'La quantité est obligatoire.',
            'lignes.*.quantite.min' => 'La quantité doit être d\'au moins 1.',
            'lignes.*.posologie.required' => 'La posologie est obligatoire.',
            'lignes.*.posologie.max' => 'La posologie ne doit pas dépasser 500 caractères.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convertir les checkbox en booléens
        $this->merge([
            'renouvelable' => $this->boolean('renouvelable'),
        ]);

        // Traiter les lignes de médicaments
        if ($this->has('lignes')) {
            $lignes = $this->input('lignes');
            foreach ($lignes as $index => $ligne) {
                $lignes[$index]['substitution_autorisee'] = $this->boolean("lignes.{$index}.substitution_autorisee");
                $lignes[$index]['urgence'] = $this->boolean("lignes.{$index}.urgence");
            }
            $this->merge(['lignes' => $lignes]);
        }

        // Si ordonnance non renouvelable, mettre nombre_renouvellements à 0
        if (!$this->boolean('renouvelable')) {
            $this->merge(['nombre_renouvellements' => 0]);
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
            // Vérifier la disponibilité des médicaments
            if ($this->has('lignes')) {
                foreach ($this->input('lignes') as $index => $ligne) {
                    if (isset($ligne['produit_id']) && isset($ligne['quantite'])) {
                        $produit = \App\Models\ProduitPharmaceutique::find($ligne['produit_id']);
                        if ($produit && $produit->stock_disponible < $ligne['quantite']) {
                            $validator->errors()->add(
                                "lignes.{$index}.quantite",
                                "Stock insuffisant pour {$produit->nom_commercial} (Stock disponible: {$produit->stock_disponible})"
                            );
                        }
                    }
                }
            }

            // Vérifier les interactions médicamenteuses (version simplifiée)
            $this->verifierInteractions($validator);

            // Vérifier les contre-indications avec les allergies du patient
            $this->verifierContrIndications($validator);
        });
    }

    /**
     * Vérifier les interactions médicamenteuses
     */
    private function verifierInteractions($validator): void
    {
        // Liste simplifiée d'interactions dangereuses connues
        $interactionsDangereuses = [
            ['aspirine', 'anticoagulant'],
            ['metformine', 'alcool'],
            ['tramadol', 'antidepresseur'],
        ];

        if ($this->has('lignes') && count($this->input('lignes')) > 1) {
            $medicaments = [];

            foreach ($this->input('lignes') as $ligne) {
                if (isset($ligne['produit_id'])) {
                    $produit = \App\Models\ProduitPharmaceutique::find($ligne['produit_id']);
                    if ($produit) {
                        $medicaments[] = strtolower($produit->dci);
                    }
                }
            }

            // Vérifier les interactions
            foreach ($interactionsDangereuses as $interaction) {
                $found = array_intersect($interaction, $medicaments);
                if (count($found) == count($interaction)) {
                    $validator->errors()->add(
                        'lignes',
                        'Attention : Interaction médicamenteuse détectée entre ' . implode(' et ', $interaction)
                    );
                }
            }
        }
    }

    /**
     * Vérifier les contre-indications avec les allergies du patient
     */
    private function verifierContrIndications($validator): void
    {
        if ($this->has('patient_id') && $this->has('lignes')) {
            $patient = \App\Models\User::find($this->input('patient_id'));

            if ($patient && $patient->dossierMedical && $patient->dossierMedical->allergies) {
                $allergies = array_map('strtolower', $patient->dossierMedical->allergies);

                foreach ($this->input('lignes') as $index => $ligne) {
                    if (isset($ligne['produit_id'])) {
                        $produit = \App\Models\ProduitPharmaceutique::find($ligne['produit_id']);
                        if ($produit) {
                            foreach ($allergies as $allergie) {
                                if (stripos($produit->dci, $allergie) !== false) {
                                    $validator->errors()->add(
                                        "lignes.{$index}.produit_id",
                                        "Attention : Le patient est allergique à {$allergie}"
                                    );
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
