<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\CompagnieAssurance;
use Carbon\Carbon;

class ContratAssuranceSeeder extends Seeder
{
    /**
        * Run the database seeds.
        */
    public function run(): void
    {
        $patients = User::role('patient')->get();
        $assureurs = CompagnieAssurance::all();

        if ($patients->isEmpty() || $assureurs->isEmpty()) {
            $this->command->warn('Aucun patient ou assureur trouvé. Veuillez exécuter UsersSeeder et CompagniesAssuranceSeeder.');
            return;
        }

        $typesContrats = ['individuel', 'famille', 'entreprise', 'collectif'];
        $rows = [];

        foreach ($patients as $patient) {
            $assureur = $assureurs->random();
            $type = $typesContrats[array_rand($typesContrats)];

            $dateDebut = Carbon::now()->subMonths(rand(0, 12));
            $dateFin = (clone $dateDebut)->addYear();
            $statut = collect(['actif', 'suspendu', 'expire'])->random();
            $plafondAnnuel = $this->fakerPlafond($type);
            $plafondConsomme = $statut === 'actif'
                ? round($plafondAnnuel * (rand(5, 50) / 100) * (max($dateDebut->diffInMonths(now()), 1) / 12), 2)
                : 0;
            $plafondRestant = max($plafondAnnuel - $plafondConsomme, 0);

            $rows[] = [
                'id' => Str::uuid(),
                'numero_contrat' => $this->generateNumeroContrat($assureur->nom_assureur ?? $assureur->nom_commercial ?? 'ASS'),
                'patient_id' => $patient->id,
                'assureur_id' => $assureur->id,
                'type_contrat' => $type,
                'formule' => 'standard',
                'numero_police' => 'POL-' . rand(100000, 999999),
                'numero_adherent' => 'ADH-' . Str::upper(Str::random(8)),
                'date_debut' => $dateDebut,
                'date_fin' => $dateFin,
                'date_renouvellement' => $dateFin,
                'auto_renouvellement' => true,
                'statut' => $statut,
                'plafond_annuel' => $plafondAnnuel,
                'plafond_consomme' => $plafondConsomme,
                'plafond_restant' => $plafondRestant,
                'plafonds_par_categorie' => null,
                'franchise_annuelle' => 0,
                'franchise_consommee' => 0,
                'delai_carence' => 0,
                'fin_carence' => null,
                'exclusions' => json_encode([]),
                'restrictions' => null,
                'maternite_couverte' => true,
                'dentaire_couvert' => true,
                'optique_couvert' => true,
                'prevention_couverte' => true,
                'beneficiaires' => null,
                'nombre_beneficiaires' => rand(1, 4),
                'carte_assure' => null,
                'attestation' => null,
                'date_emission_carte' => null,
                'validite_carte' => null,
                'cotisation_mensuelle' => null,
                'cotisation_annuelle' => null,
                'cotisation_a_jour' => true,
                'derniere_cotisation' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('contrats_assurance')->insert($rows);
        $this->command->info(count($rows) . ' contrats d\'assurance créés avec succès.');
    }

    private function generateNumeroContrat(?string $assureurName, string $prefix = 'CTR'): string
    {
        $name = $assureurName ?: 'ASSUREUR';
        $assureurCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $name), 0, 3)) ?: 'ASS';
        $year = date('Y');
        $random = str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);

        return "{$prefix}-{$assureurCode}-{$year}-{$random}";
    }

    private function fakerPlafond(string $type): float
    {
        return match ($type) {
            'individuel' => rand(1_000_000, 3_000_000),
            'famille' => rand(3_000_000, 8_000_000),
            'entreprise' => rand(5_000_000, 12_000_000),
            default => rand(2_000_000, 5_000_000),
        };
    }
}
