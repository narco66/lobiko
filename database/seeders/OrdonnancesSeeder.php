<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ordonnance;
use App\Models\OrdonnanceLigne;
use App\Models\Consultation;
use App\Models\ProduitPharmaceutique;
use App\Models\User;
use App\Models\StructureMedicale;
use Carbon\Carbon;

class OrdonnancesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les données nécessaires
        $consultations = Consultation::all();
        $patients = User::role('patient')->get();
        $praticiens = User::role('praticien')->get();
        $structures = StructureMedicale::whereIn('type', ['cabinet', 'clinique', 'hopital'])->get();
        $produits = ProduitPharmaceutique::all();

        if ($consultations->isEmpty() || $produits->isEmpty()) {
            $this->command->warn('Aucune consultation ou produit trouvé. Veuillez d\'abord exécuter les seeders nécessaires.');
            return;
        }

        $diagnostics = [
            'Grippe saisonnière',
            'Infection respiratoire',
            'Hypertension artérielle',
            'Diabète type 2',
            'Gastro-entérite',
            'Migraine',
            'Infection urinaire',
            'Bronchite',
            'Allergie saisonnière',
            'Douleurs articulaires',
            'Anxiété',
            'Insomnie',
            'Infection cutanée',
            'Conjonctivite',
            'Otite',
        ];

        $posologies = [
            '1 comprimé matin et soir',
            '2 comprimés 3 fois par jour',
            '1 gélule le matin à jeun',
            '1 sachet dans un verre d\'eau 3 fois par jour',
            '20 gouttes 3 fois par jour',
            '1 cuillère à soupe matin, midi et soir',
            '1 comprimé au coucher',
            '1 injection par semaine',
            '2 comprimés toutes les 8 heures',
            '1 application locale 2 fois par jour',
        ];

        $instructions = [
            'Prendre pendant les repas',
            'À prendre à jeun',
            'Ne pas dépasser la dose prescrite',
            'Éviter l\'alcool pendant le traitement',
            'Conserver au réfrigérateur',
            'Agiter avant emploi',
            'À prendre avec un grand verre d\'eau',
            'Espacer de 2h avec les produits laitiers',
            'Continuer le traitement jusqu\'à la fin',
            'Consulter si pas d\'amélioration après 3 jours',
        ];

        $ordonnances = [];
        $lignesOrdonnances = [];

        // Créer des ordonnances pour 70% des consultations
        $consultationsAvecOrdonnance = $consultations->random(ceil($consultations->count() * 0.7));

        foreach ($consultationsAvecOrdonnance as $consultation) {
            $dateOrdonnance = $consultation->date_consultation;
            $validiteJours = rand(7, 30);

            // Déterminer le statut basé sur la date
            $dateExpiration = Carbon::parse($dateOrdonnance)->addDays($validiteJours);
            if ($dateExpiration < now()) {
                $statut = rand(0, 1) ? 'dispensee' : 'expiree';
            } else {
                $statut = rand(0, 2) ? 'active' : 'dispensee';
            }

            $ordonnance = [
                'numero_ordonnance' => $this->generateNumeroOrdonnance(),
                'consultation_id' => $consultation->id,
                'patient_id' => $consultation->patient_id,
                'praticien_id' => $consultation->praticien_id,
                'structure_id' => $consultation->structure_id,
                'date_ordonnance' => $dateOrdonnance,
                'validite_jours' => $validiteJours,
                'date_expiration' => $dateExpiration,
                'diagnostic' => $diagnostics[array_rand($diagnostics)],
                'observations' => rand(0, 1) ? 'RAS' : 'Surveillance particulière requise',
                'signature_numerique' => hash('sha256', $consultation->id . config('app.key')),
                'qr_code' => null, // Sera généré après création
                'statut' => $statut,
                'type_ordonnance' => rand(0, 10) > 8 ? 'secure' : 'normale',
                'renouvelable' => rand(0, 1) == 1,
                'nombre_renouvellements' => rand(0, 1) ? rand(1, 3) : 0,
                'renouvellements_effectues' => 0,
                'metadata' => json_encode([
                    'temperature' => rand(36, 39) . '.' . rand(0, 9) . '°C',
                    'tension' => rand(11, 14) . '/' . rand(7, 9),
                    'poids' => rand(50, 95) . ' kg',
                ]),
                'created_at' => $dateOrdonnance,
                'updated_at' => now(),
            ];

            $ordonnances[] = $ordonnance;
        }

        // Insérer les ordonnances
        foreach (array_chunk($ordonnances, 100) as $chunk) {
            Ordonnance::insert($chunk);
        }

        $this->command->info(count($ordonnances) . ' ordonnances créées.');

        // Récupérer les ordonnances créées pour ajouter les lignes
        $ordonnancesCreees = Ordonnance::orderBy('id', 'desc')->limit(count($ordonnances))->get();

        foreach ($ordonnancesCreees as $ordonnance) {
            // Générer le QR code
            $ordonnance->generateQrCode();

            // Nombre de médicaments par ordonnance (entre 1 et 5)
            $nombreMedicaments = rand(1, 5);
            $produitsOrdonnance = $produits->random($nombreMedicaments);

            foreach ($produitsOrdonnance as $produit) {
                $quantite = rand(1, 3);
                $dureeTraitement = rand(3, 30);

                $ligneOrdonnance = [
                    'ordonnance_id' => $ordonnance->id,
                    'produit_id' => $produit->id,
                    'dci' => $produit->dci,
                    'nom_commercial' => $produit->nom_commercial,
                    'dosage' => $produit->dosage,
                    'forme' => $produit->forme,
                    'quantite' => $quantite,
                    'posologie' => $posologies[array_rand($posologies)],
                    'duree_traitement' => $dureeTraitement,
                    'unite_duree' => 'jours',
                    'voie_administration' => $this->getVoieAdministration($produit->forme),
                    'moment_prise' => json_encode($this->getMomentsPrise()),
                    'instructions_speciales' => rand(0, 1) ? $instructions[array_rand($instructions)] : null,
                    'substitution_autorisee' => rand(0, 1) == 1,
                    'urgence' => rand(0, 10) == 1,
                    'dispensee' => $ordonnance->statut === 'dispensee',
                    'quantite_dispensee' => $ordonnance->statut === 'dispensee' ? $quantite : 0,
                    'date_dispensation' => $ordonnance->statut === 'dispensee' ? $ordonnance->date_ordonnance->addDays(rand(0, 2)) : null,
                    'pharmacie_id' => $ordonnance->statut === 'dispensee' ? StructureMedicale::where('type', 'pharmacie')->inRandomOrder()->first()->id : null,
                    'prix_unitaire' => $produit->prix_unitaire,
                    'montant_total' => $quantite * $produit->prix_unitaire,
                    'metadata' => json_encode([
                        'stock_initial' => $produit->stock_disponible,
                        'lot' => 'LOT-' . strtoupper($this->str_random(8)),
                    ]),
                    'created_at' => $ordonnance->date_ordonnance,
                    'updated_at' => now(),
                ];

                $lignesOrdonnances[] = $ligneOrdonnance;
            }
        }

        // Insérer les lignes d'ordonnance
        foreach (array_chunk($lignesOrdonnances, 100) as $chunk) {
            OrdonnanceLigne::insert($chunk);
        }

        $this->command->info(count($lignesOrdonnances) . ' lignes d\'ordonnance créées.');

        // Créer quelques ordonnances sans consultation (ordonnances directes)
        $this->createOrdonnancesDirectes($patients, $praticiens, $structures, $produits);
    }

    /**
     * Créer des ordonnances directes (sans consultation)
     */
    private function createOrdonnancesDirectes($patients, $praticiens, $structures, $produits)
    {
        $nombreOrdonnancesDirectes = 20;

        for ($i = 0; $i < $nombreOrdonnancesDirectes; $i++) {
            $patient = $patients->random();
            $praticien = $praticiens->random();
            $structure = $structures->random();

            $ordonnance = Ordonnance::create([
                'consultation_id' => null,
                'patient_id' => $patient->id,
                'praticien_id' => $praticien->id,
                'structure_id' => $structure->id,
                'date_ordonnance' => now()->subDays(rand(0, 30)),
                'validite_jours' => 15,
                'diagnostic' => 'Renouvellement de traitement',
                'observations' => 'Ordonnance de renouvellement',
                'statut' => 'active',
                'type_ordonnance' => 'normale',
                'renouvelable' => true,
                'nombre_renouvellements' => 3,
                'metadata' => [
                    'type' => 'renouvellement',
                    'ordonnance_origine' => 'ORD-' . rand(1000, 9999),
                ],
            ]);

            // Ajouter 1 à 3 médicaments
            $nombreMedicaments = rand(1, 3);
            $produitsOrdonnance = $produits->random($nombreMedicaments);

            foreach ($produitsOrdonnance as $produit) {
                OrdonnanceLigne::create([
                    'ordonnance_id' => $ordonnance->id,
                    'produit_id' => $produit->id,
                    'dci' => $produit->dci,
                    'nom_commercial' => $produit->nom_commercial,
                    'dosage' => $produit->dosage,
                    'forme' => $produit->forme,
                    'quantite' => 1,
                    'posologie' => '1 comprimé par jour',
                    'duree_traitement' => 30,
                    'unite_duree' => 'jours',
                    'voie_administration' => 'Orale',
                    'substitution_autorisee' => true,
                    'prix_unitaire' => $produit->prix_unitaire,
                ]);
            }
        }

        $this->command->info($nombreOrdonnancesDirectes . ' ordonnances directes créées.');
    }

    /**
     * Générer un numéro d'ordonnance unique
     */
    private function generateNumeroOrdonnance(): string
    {
        $prefix = 'ORD';
        $year = date('Y');
        $random = strtoupper($this->str_random(8));

        return "{$prefix}-{$year}-{$random}";
    }

    /**
     * Déterminer la voie d'administration selon la forme
     */
    private function getVoieAdministration(string $forme): string
    {
        $voies = [
            'comprime' => 'Orale',
            'gelule' => 'Orale',
            'sirop' => 'Orale',
            'suspension' => 'Orale',
            'injection' => 'Injectable',
            'creme' => 'Cutanée',
            'pommade' => 'Cutanée',
            'gel' => 'Cutanée',
            'collyre' => 'Ophtalmique',
            'suppositoire' => 'Rectale',
            'spray' => 'Nasale',
            'inhalateur' => 'Inhalation',
        ];

        $formeLower = strtolower($forme);

        foreach ($voies as $key => $voie) {
            if (str_contains($formeLower, $key)) {
                return $voie;
            }
        }

        return 'Orale'; // Par défaut
    }

    /**
     * Générer les moments de prise
     */
    private function getMomentsPrise(): array
    {
        $moments = ['matin', 'midi', 'soir', 'coucher'];
        $nombreMoments = rand(1, 3);

        return array_slice($moments, 0, $nombreMoments);
    }

    /**
     * Helper pour générer une chaîne aléatoire (pour Laravel < 9)
     */
    private function str_random($length = 8): string
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }
}
