<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (app()->environment('testing')) {
            $this->call(TestsBaseSeeder::class);
            return;
        }

        $shouldClean = (bool) env('SEED_CLEAN_DATABASE', false);
        $isSafeEnv = app()->environment(['local', 'development']);

        if ($shouldClean && $isSafeEnv) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            $this->cleanDatabase();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        // Seeders dans l'ordre de dépendance
        $this->call([
            // 1. Données de référence et configuration
            RolesAndPermissionsSeeder::class,
            PlanComptableSeeder::class,
            MedicalReferentialsSeeder::class,

            // 2. Utilisateurs et structures
            UsersSeeder::class,
            StructuresMedicalesSeeder::class,

            // Compte admin dédié
            AdminSeeder::class,

            // 3. Catalogues et tarifs
            ActesMedicauxSeeder::class,
            ProduitsPharmaceutiquesSeeder::class,
            GrillesTarifairesSeeder::class,
            ForfaitsSeeder::class,

            // 4. Assurances
            CompagniesAssuranceSeeder::class,
            ContratAssuranceSeeder::class,

            // 5. Données médicales (si environnement de développement)
            ...(app()->environment('local', 'development') ? [
                DossiersMedicauxSeeder::class,
                RendezVousSeeder::class,
                ConsultationsSeeder::class,
                OrdonnancesSeeder::class,
                FacturesSeeder::class,
            ] : []),

            // 6. Contenus marketing / front-office
            MarketingSeeder::class,
        ]);

        $this->command->info('Base de données initialisée avec succès!');
        $this->displayCredentials();
    }

    /**
     * Nettoyer les tables
     */
    private function cleanDatabase(): void
    {
        $tables = [
            'evaluations',
            'litiges',
            'audit_logs',
            'notifications',
            'contact_messages',
            'testimonials',
            'article_categories',
            'articles',
            'services',
            'statistiques',
            'faqs',
            'partners',
            'newsletter_subscribers',
            'custom_pages',
            'banners',
            'team_members',
            'job_applications',
            'job_offers',
            'rapprochements_bancaires',
            'lignes_ecritures',
            'ecritures_comptables',
            'reversements',
            'paiements',
            'facture_lignes',
            'factures',
            'devis_lignes',
            'devis',
            'remboursements_assurance',
            'prises_en_charge',
            'contrats_assurance',
            'compagnies_assurance',
            'commande_lignes',
            'commandes_pharmacie',
            'stocks_pharmacie',
            'ordonnance_lignes',
            'ordonnances',
            'dossiers_medicaux',
            'consultations',
            'rendez_vous',
            'forfaits',
            'grilles_tarifaires',
            'produits_pharmaceutiques',
            'actes_medicaux',
            'user_structure',
            'structures_medicales',
            'model_has_roles',
            'model_has_permissions',
            'role_has_permissions',
            'users',
            'permissions',
            'roles',
            'journaux_comptables',
            'plan_comptable',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }
    }

    /**
     * Afficher les identifiants de connexion
     */
    private function displayCredentials(): void
    {
        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('IDENTIFIANTS DE CONNEXION');
        $this->command->info('========================================');
        $this->command->info('');
        $this->command->table(
            ['Rôle', 'Email', 'Mot de passe'],
            [
                ['Admin', 'admin@lobiko.com', 'Admin@2025'],
                ['Médecin', 'dr.martin@lobiko.com', 'Medecin@2025'],
                ['Patient', 'patient.test@lobiko.com', 'Patient@2025'],
                ['Pharmacien', 'pharmacie.centrale@lobiko.com', 'Pharmacie@2025'],
                ['Assureur', 'assurance@lobiko.com', 'Assurance@2025'],
                ['Comptable', 'comptable@lobiko.com', 'Comptable@2025'],
            ]
        );
        $this->command->info('');
        $this->command->info('URL: ' . config('app.url'));
        $this->command->info('');
    }
}
