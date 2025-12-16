<?php

namespace Database\Factories;

use App\Models\ContratAssurance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class ContratAssuranceFactory extends Factory
{
    protected $model = ContratAssurance::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dateDebut = $this->faker->dateTimeBetween('-1 year', 'now');
        $dateFin = Carbon::instance($dateDebut)->addYear();
        $plafondAnnuel = $this->faker->randomElement([1000000, 2000000, 5000000, 10000000, 20000000]);
        $tauxCouverture = $this->faker->randomElement([60, 70, 80, 85, 90, 100]);

        // Calculer une consommation réaliste du plafond
        $moisEcoules = Carbon::instance($dateDebut)->diffInMonths(now());
        $tauxConsommation = $moisEcoules > 0 ? $this->faker->randomFloat(2, 0, 0.6) : 0;
        $plafondConsomme = $plafondAnnuel * $tauxConsommation * ($moisEcoules / 12);

        $exclusions = $this->faker->randomElements([
            'chirurgie_esthetique',
            'implants_dentaires',
            'lunettes_solaires',
            'medecine_douce',
            'cure_thermale',
            'chirurgie_refractive',
            'protheses_auditives',
        ], $this->faker->numberBetween(0, 3));

        return [
            'patient_id' => User::factory()->patient(),
            'assurance_id' => User::factory()->assureur(),
            'numero_contrat' => $this->generateNumeroContrat(),
            'type_contrat' => $this->faker->randomElement(['individuel', 'famille', 'entreprise', 'premium', 'etudiant']),
            'taux_couverture' => $tauxCouverture,
            'plafond_annuel' => $plafondAnnuel,
            'plafond_consomme' => round($plafondConsomme, 2),
            'exclusions' => $exclusions,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'statut' => $this->determinerStatut($dateDebut, $dateFin),
            'documents' => [
                'carte_assure' => '/documents/cartes/' . $this->faker->uuid . '.pdf',
                'attestation' => '/documents/attestations/' . $this->faker->uuid . '.pdf',
                'conditions_generales' => '/documents/cg/' . $this->faker->uuid . '.pdf',
            ],
            'metadata' => [
                'numero_adherent' => 'ADH-' . $this->faker->numerify('######'),
                'beneficiaires' => $this->faker->numberBetween(0, 5),
                'prime_mensuelle' => $this->faker->numberBetween(5000, 100000),
                'franchise' => $this->faker->numberBetween(0, 50000),
                'delai_carence' => $this->faker->numberBetween(0, 90),
                'reseau_soins' => $this->faker->boolean(70),
            ],
        ];
    }

    /**
     * Indicate that the contract is active.
     */
    public function actif(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'actif',
            'date_debut' => now()->subMonths(3),
            'date_fin' => now()->addMonths(9),
        ]);
    }

    /**
     * Indicate that the contract is expired.
     */
    public function expire(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'expire',
            'date_debut' => now()->subYears(2),
            'date_fin' => now()->subMonths(3),
        ]);
    }

    /**
     * Indicate that the contract is suspended.
     */
    public function suspendu(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'suspendu',
            'metadata' => array_merge($attributes['metadata'] ?? [], [
                'motif_suspension' => $this->faker->randomElement([
                    'Non-paiement des primes',
                    'Fraude suspectée',
                    'Documents manquants',
                    'Demande du client',
                ]),
                'date_suspension' => now()->subDays($this->faker->numberBetween(1, 30)),
            ]),
        ]);
    }

    /**
     * Indicate that the contract has high coverage.
     */
    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'type_contrat' => 'premium',
            'taux_couverture' => $this->faker->numberBetween(90, 100),
            'plafond_annuel' => $this->faker->numberBetween(20000000, 50000000),
            'exclusions' => [],
        ]);
    }

    /**
     * Indicate that the contract is for a company.
     */
    public function entreprise(): static
    {
        return $this->state(fn (array $attributes) => [
            'type_contrat' => 'entreprise',
            'taux_couverture' => 85,
            'plafond_annuel' => 15000000,
            'metadata' => array_merge($attributes['metadata'] ?? [], [
                'entreprise' => $this->faker->company,
                'nombre_employes' => $this->faker->numberBetween(10, 500),
                'secteur_activite' => $this->faker->randomElement([
                    'Technologie',
                    'Finance',
                    'Industrie',
                    'Services',
                    'Commerce',
                ]),
            ]),
        ]);
    }

    /**
     * Generate a unique contract number.
     */
    private function generateNumeroContrat(): string
    {
        $prefix = $this->faker->randomElement(['CTR', 'POL', 'ASS']);
        $year = date('Y');
        $sequence = $this->faker->numerify('######');

        return "{$prefix}-{$year}-{$sequence}";
    }

    /**
     * Determine the contract status based on dates.
     */
    private function determinerStatut($dateDebut, $dateFin): string
    {
        $now = now();

        if ($dateFin < $now) {
            return 'expire';
        }

        if ($dateDebut > $now) {
            return 'en_attente';
        }

        // 10% de chance d'être suspendu
        if ($this->faker->boolean(10)) {
            return 'suspendu';
        }

        return 'actif';
    }
}
