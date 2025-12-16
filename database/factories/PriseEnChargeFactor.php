<?php

namespace Database\Factories;

use App\Models\PriseEnCharge;
use App\Models\ContratAssurance;
use App\Models\Facture;
use App\Models\Devis;
use App\Models\User;
use App\Models\StructureMedicale;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class PriseEnChargeFactory extends Factory
{
    protected $model = PriseEnCharge::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $montantDemande = $this->faker->randomFloat(2, 5000, 500000);
        $tauxPec = $this->faker->randomElement([60, 70, 80, 85, 90, 100]);
        $montantAccorde = $montantDemande * ($tauxPec / 100);

        $dateDemande = $this->faker->dateTimeBetween('-3 months', 'now');
        $validiteJours = $this->faker->randomElement([15, 30, 45, 60]);
        $dateExpiration = Carbon::instance($dateDemande)->addDays($validiteJours);

        $statut = $this->determinerStatut($dateDemande);
        $dateReponse = in_array($statut, ['acceptee', 'refusee', 'utilisee'])
            ? $this->faker->dateTimeBetween($dateDemande, 'now')
            : null;

        $typesPec = [
            'consultation',
            'hospitalisation',
            'chirurgie',
            'imagerie',
            'analyses',
            'pharmacie',
            'soins_dentaires',
            'optique',
            'maternite',
            'urgence',
        ];

        $motifs = [
            'Consultation spécialisée',
            'Hospitalisation programmée',
            'Intervention chirurgicale',
            'Examens médicaux',
            'Traitement médical',
            'Soins urgents',
            'Suivi de grossesse',
            'Rééducation fonctionnelle',
            'Bilan de santé',
            'Soins dentaires',
        ];

        return [
            'numero_pec' => $this->generateNumeroPec(),
            'contrat_id' => ContratAssurance::factory(),
            'facture_id' => $this->faker->boolean(30) ? Facture::factory() : null,
            'devis_id' => $this->faker->boolean(40) ? Devis::factory() : null,
            'patient_id' => User::factory()->patient(),
            'praticien_id' => User::factory()->praticien(),
            'structure_id' => StructureMedicale::factory(),
            'type_pec' => $this->faker->randomElement($typesPec),
            'montant_demande' => $montantDemande,
            'montant_accorde' => $statut === 'acceptee' ? $montantAccorde : ($statut === 'refusee' ? 0 : null),
            'taux_pec' => $statut === 'acceptee' ? $tauxPec : ($statut === 'refusee' ? 0 : null),
            'motif' => $this->faker->randomElement($motifs),
            'statut' => $statut,
            'date_demande' => $dateDemande,
            'date_reponse' => $dateReponse,
            'validite_jours' => $validiteJours,
            'date_expiration' => $dateExpiration,
            'justificatifs' => $this->generateJustificatifs(),
            'commentaire_assurance' => $this->generateCommentaireAssurance($statut),
            'metadata' => $this->generateMetadata($statut),
        ];
    }

    /**
     * Indicate that the PEC is pending.
     */
    public function enAttente(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'en_attente',
            'date_demande' => now(),
            'date_reponse' => null,
            'montant_accorde' => null,
            'taux_pec' => null,
            'commentaire_assurance' => null,
        ]);
    }

    /**
     * Indicate that the PEC is accepted.
     */
    public function acceptee(): static
    {
        return $this->state(function (array $attributes) {
            $montantAccorde = $attributes['montant_demande'] * 0.8; // 80% de couverture

            return [
                'statut' => 'acceptee',
                'date_reponse' => now(),
                'montant_accorde' => $montantAccorde,
                'taux_pec' => 80,
                'commentaire_assurance' => 'Prise en charge acceptée selon les termes du contrat',
            ];
        });
    }

    /**
     * Indicate that the PEC is refused.
     */
    public function refusee(): static
    {
        $motifsRefus = [
            'Acte non couvert par le contrat',
            'Plafond annuel dépassé',
            'Délai de carence non respecté',
            'Documents justificatifs insuffisants',
            'Exclusion contractuelle',
            'Non-respect du parcours de soins',
            'Demande hors délai',
        ];

        return $this->state(fn (array $attributes) => [
            'statut' => 'refusee',
            'date_reponse' => now(),
            'montant_accorde' => 0,
            'taux_pec' => 0,
            'commentaire_assurance' => $this->faker->randomElement($motifsRefus),
        ]);
    }

    /**
     * Indicate that the PEC has been used.
     */
    public function utilisee(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'utilisee',
            'date_reponse' => now()->subDays($this->faker->numberBetween(5, 30)),
            'metadata' => array_merge($attributes['metadata'] ?? [], [
                'date_utilisation' => now()->subDays($this->faker->numberBetween(1, 5)),
                'facture_associee' => 'FACT-' . $this->faker->numerify('######'),
            ]),
        ]);
    }

    /**
     * Indicate that the PEC is for emergency.
     */
    public function urgence(): static
    {
        return $this->state(fn (array $attributes) => [
            'type_pec' => 'urgence',
            'validite_jours' => 3,
            'motif' => 'Prise en charge urgente',
            'metadata' => array_merge($attributes['metadata'] ?? [], [
                'urgence' => true,
                'code_urgence' => $this->faker->randomElement(['U1', 'U2', 'U3']),
                'delai_traitement' => 'immediat',
            ]),
        ]);
    }

    /**
     * Indicate that the PEC is for hospitalization.
     */
    public function hospitalisation(): static
    {
        $montantDemande = $this->faker->randomFloat(2, 100000, 2000000);

        return $this->state(fn (array $attributes) => [
            'type_pec' => 'hospitalisation',
            'montant_demande' => $montantDemande,
            'montant_accorde' => $montantDemande * 0.85,
            'taux_pec' => 85,
            'validite_jours' => 60,
            'motif' => 'Hospitalisation programmée',
            'metadata' => array_merge($attributes['metadata'] ?? [], [
                'duree_sejour' => $this->faker->numberBetween(1, 10) . ' jours',
                'type_chambre' => $this->faker->randomElement(['individuelle', 'double', 'commune']),
                'service' => $this->faker->randomElement(['Médecine', 'Chirurgie', 'Maternité', 'Pédiatrie']),
            ]),
        ]);
    }

    /**
     * Generate a unique PEC number.
     */
    private function generateNumeroPec(): string
    {
        $prefix = 'PEC';
        $year = date('Y');
        $month = date('m');
        $random = strtoupper($this->faker->bothify('??????'));

        return "{$prefix}-{$year}{$month}-{$random}";
    }

    /**
     * Determine the status based on date.
     */
    private function determinerStatut($dateDemande): string
    {
        $joursDepuisDemande = Carbon::instance($dateDemande)->diffInDays(now());

        if ($joursDepuisDemande < 3) {
            // Récent : 60% en attente, 30% accepté, 10% refusé
            $rand = $this->faker->numberBetween(1, 100);
            if ($rand <= 60) return 'en_attente';
            if ($rand <= 90) return 'acceptee';
            return 'refusee';
        }

        if ($joursDepuisDemande < 30) {
            // Moyen : 10% en attente, 60% accepté, 20% refusé, 10% utilisé
            $rand = $this->faker->numberBetween(1, 100);
            if ($rand <= 10) return 'en_attente';
            if ($rand <= 70) return 'acceptee';
            if ($rand <= 90) return 'refusee';
            return 'utilisee';
        }

        // Ancien : 70% utilisé, 20% expiré, 10% accepté
        $rand = $this->faker->numberBetween(1, 100);
        if ($rand <= 70) return 'utilisee';
        if ($rand <= 90) return 'expiree';
        return 'acceptee';
    }

    /**
     * Generate justificatifs documents.
     */
    private function generateJustificatifs(): array
    {
        $justificatifs = [];

        if ($this->faker->boolean(80)) {
            $justificatifs[] = [
                'type' => 'prescription',
                'fichier' => '/documents/pec/prescription_' . $this->faker->uuid . '.pdf',
                'date_upload' => now()->subDays($this->faker->numberBetween(1, 10)),
            ];
        }

        if ($this->faker->boolean(60)) {
            $justificatifs[] = [
                'type' => 'devis',
                'fichier' => '/documents/pec/devis_' . $this->faker->uuid . '.pdf',
                'date_upload' => now()->subDays($this->faker->numberBetween(1, 10)),
            ];
        }

        if ($this->faker->boolean(40)) {
            $justificatifs[] = [
                'type' => 'compte_rendu',
                'fichier' => '/documents/pec/cr_' . $this->faker->uuid . '.pdf',
                'date_upload' => now()->subDays($this->faker->numberBetween(1, 10)),
            ];
        }

        return $justificatifs;
    }

    /**
     * Generate insurance comment based on status.
     */
    private function generateCommentaireAssurance($statut): ?string
    {
        switch ($statut) {
            case 'acceptee':
                return $this->faker->randomElement([
                    'Prise en charge accordée selon les conditions du contrat',
                    'Accord pour la prise en charge dans la limite du plafond',
                    'PEC validée - Respecter le parcours de soins',
                    'Acceptation sous réserve de présentation des justificatifs',
                    null,
                ]);

            case 'refusee':
                return $this->faker->randomElement([
                    'Acte non couvert par les garanties du contrat',
                    'Plafond annuel dépassé',
                    'Documents justificatifs insuffisants',
                    'Délai de carence non respecté',
                    'Exclusion contractuelle applicable',
                ]);

            case 'utilisee':
                return 'Prise en charge consommée et clôturée';

            default:
                return null;
        }
    }

    /**
     * Generate metadata based on status.
     */
    private function generateMetadata($statut): array
    {
        $metadata = [
            'canal_demande' => $this->faker->randomElement(['web', 'mobile', 'agence', 'telephone']),
            'agent_traitement' => $this->faker->name,
        ];

        if ($statut === 'acceptee' || $statut === 'utilisee') {
            $metadata['numero_autorisation'] = 'AUTH-' . $this->faker->numerify('########');
            $metadata['delai_traitement_heures'] = $this->faker->numberBetween(1, 72);
        }

        if ($statut === 'refusee') {
            $metadata['code_refus'] = $this->faker->randomElement(['R01', 'R02', 'R03', 'R04', 'R05']);
            $metadata['possibilite_recours'] = $this->faker->boolean(30);
        }

        if ($statut === 'utilisee') {
            $metadata['date_facturation'] = now()->subDays($this->faker->numberBetween(1, 20));
            $metadata['montant_facture'] = $this->faker->randomFloat(2, 10000, 500000);
        }

        return $metadata;
    }
}
