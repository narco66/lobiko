<?php

namespace Database\Factories;

use App\Models\Ordonnance;
use App\Models\Consultation;
use App\Models\User;
use App\Models\StructureMedicale;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class OrdonnanceFactory extends Factory
{
    protected $model = Ordonnance::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dateOrdonnance = $this->faker->dateTimeBetween('-6 months', 'now');
        $validiteJours = $this->faker->numberBetween(7, 30);
        $dateExpiration = Carbon::instance($dateOrdonnance)->addDays($validiteJours);

        $diagnostics = [
            'Hypertension artérielle',
            'Diabète type 2',
            'Infection respiratoire haute',
            'Gastro-entérite aiguë',
            'Migraine chronique',
            'Bronchite aiguë',
            'Sinusite',
            'Infection urinaire',
            'Allergie saisonnière',
            'Anxiété généralisée',
            'Insomnie',
            'Lombalgie',
            'Arthrose',
            'Reflux gastro-œsophagien',
            'Conjonctivite virale',
            'Otite moyenne',
            'Dermatite atopique',
            'Angine streptococcique',
            'Rhinopharyngite',
            'Cystite',
        ];

        $observations = [
            'RAS - Patient stable',
            'Surveillance de la tension artérielle recommandée',
            'Contrôle glycémique dans 1 mois',
            'Repos recommandé',
            'Éviter les efforts physiques intenses',
            'Régime alimentaire à adapter',
            'Hydratation importante',
            'Suivi médical dans 2 semaines',
            'Si pas d\'amélioration, reconsulter',
            'Examens complémentaires à prévoir',
            null, // Pas d'observations dans certains cas
        ];

        return [
            'numero_ordonnance' => $this->generateNumeroOrdonnance(),
            'consultation_id' => $this->faker->boolean(80) ? Consultation::factory() : null,
            'patient_id' => User::factory()->patient(),
            'praticien_id' => User::factory()->praticien(),
            'structure_id' => StructureMedicale::factory(),
            'date_ordonnance' => $dateOrdonnance,
            'validite_jours' => $validiteJours,
            'date_expiration' => $dateExpiration,
            'diagnostic' => $this->faker->randomElement($diagnostics),
            'observations' => $this->faker->randomElement($observations),
            'signature_numerique' => hash('sha256', $this->faker->uuid . config('app.key')),
            'qr_code' => null, // Sera généré après création
            'statut' => $this->determinerStatut($dateExpiration),
            'type_ordonnance' => $this->faker->randomElement(['normale', 'normale', 'normale', 'secure', 'exception']),
            'renouvelable' => $this->faker->boolean(40),
            'nombre_renouvellements' => $this->faker->boolean(40) ? $this->faker->numberBetween(1, 3) : 0,
            'renouvellements_effectues' => 0,
            'metadata' => $this->generateMetadata(),
        ];
    }

    /**
     * Indicate that the ordonnance is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'active',
            'date_ordonnance' => now(),
            'date_expiration' => now()->addDays(15),
        ]);
    }

    /**
     * Indicate that the ordonnance has been dispensed.
     */
    public function dispensee(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'dispensee',
            'metadata' => array_merge($attributes['metadata'] ?? [], [
                'date_dispensation' => now()->subDays($this->faker->numberBetween(1, 7)),
                'pharmacie_id' => StructureMedicale::factory()->pharmacie(),
            ]),
        ]);
    }

    /**
     * Indicate that the ordonnance is expired.
     */
    public function expiree(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'expiree',
            'date_ordonnance' => now()->subMonths(2),
            'date_expiration' => now()->subMonth(),
        ]);
    }

    /**
     * Indicate that the ordonnance is renewable.
     */
    public function renouvelable(): static
    {
        return $this->state(fn (array $attributes) => [
            'renouvelable' => true,
            'nombre_renouvellements' => $this->faker->numberBetween(1, 6),
            'renouvellements_effectues' => 0,
        ]);
    }

    /**
     * Indicate that the ordonnance is secure (for controlled substances).
     */
    public function secure(): static
    {
        return $this->state(fn (array $attributes) => [
            'type_ordonnance' => 'secure',
            'validite_jours' => 3,
            'renouvelable' => false,
            'metadata' => array_merge($attributes['metadata'] ?? [], [
                'substances_controlees' => true,
                'verification_identite' => true,
                'registre_stupefiants' => true,
            ]),
        ]);
    }

    /**
     * Indicate that the ordonnance is for chronic treatment.
     */
    public function chronique(): static
    {
        $diagnosticsChroniques = [
            'Hypertension artérielle',
            'Diabète type 2',
            'Asthme',
            'Épilepsie',
            'Hypothyroïdie',
            'Hypercholestérolémie',
            'Insuffisance cardiaque',
            'BPCO',
        ];

        return $this->state(fn (array $attributes) => [
            'diagnostic' => $this->faker->randomElement($diagnosticsChroniques),
            'validite_jours' => 30,
            'renouvelable' => true,
            'nombre_renouvellements' => 6,
            'observations' => 'Traitement au long cours - Suivi régulier nécessaire',
        ]);
    }

    /**
     * Generate a unique ordonnance number.
     */
    private function generateNumeroOrdonnance(): string
    {
        $prefix = 'ORD';
        $year = date('Y');
        $random = strtoupper($this->faker->bothify('??######'));

        return "{$prefix}-{$year}-{$random}";
    }

    /**
     * Determine the status based on expiration date.
     */
    private function determinerStatut($dateExpiration): string
    {
        if ($dateExpiration < now()) {
            // 60% dispensée, 40% expirée pour les ordonnances passées
            return $this->faker->boolean(60) ? 'dispensee' : 'expiree';
        }

        // Pour les ordonnances valides
        // 30% déjà dispensées, 70% actives
        return $this->faker->boolean(30) ? 'dispensee' : 'active';
    }

    /**
     * Generate metadata for the ordonnance.
     */
    private function generateMetadata(): array
    {
        $metadata = [
            'constantes' => [
                'temperature' => $this->faker->randomFloat(1, 36.0, 39.5) . '°C',
                'tension' => $this->faker->numberBetween(10, 16) . '/' . $this->faker->numberBetween(6, 10),
                'poids' => $this->faker->numberBetween(45, 120) . ' kg',
                'taille' => $this->faker->numberBetween(150, 200) . ' cm',
            ],
        ];

        // Ajouter parfois des informations supplémentaires
        if ($this->faker->boolean(30)) {
            $metadata['allergies_notees'] = $this->faker->randomElements([
                'Pénicilline',
                'Aspirine',
                'Iode',
                'Latex',
                'Arachides',
            ], $this->faker->numberBetween(0, 2));
        }

        if ($this->faker->boolean(20)) {
            $metadata['examens_demandes'] = $this->faker->randomElements([
                'NFS',
                'Glycémie',
                'Bilan lipidique',
                'Créatinine',
                'ECBU',
                'Radio thorax',
            ], $this->faker->numberBetween(1, 3));
        }

        return $metadata;
    }
}
