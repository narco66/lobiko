<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Créer les permissions
        $permissions = $this->createPermissions();

        // Créer les rôles et assigner les permissions
        $this->createRoles($permissions);

        $this->command->info('Rôles et permissions créés avec succès!');
    }

    /**
     * Créer toutes les permissions
     */
    private function createPermissions(): array
    {
        $permissions = [];

        // Permissions par module
        $modules = [
            'users' => ['view', 'create', 'edit', 'delete', 'verify'],
            'structures' => ['view', 'create', 'edit', 'delete', 'verify'],
            'consultations' => ['view', 'create', 'edit', 'delete', 'validate'],
            'ordonnances' => ['view', 'create', 'edit', 'delete', 'dispense'],
            'rendez-vous' => ['view', 'create', 'edit', 'delete', 'confirm'],
            'dossiers-medicaux' => ['view', 'create', 'edit', 'delete', 'share'],
            'factures' => ['view', 'create', 'edit', 'delete', 'validate'],
            'paiements' => ['view', 'create', 'edit', 'delete', 'validate'],
            'pec' => ['view', 'create', 'edit', 'delete', 'approve'],
            'reversements' => ['view', 'create', 'edit', 'delete', 'validate'],
            'stocks' => ['view', 'create', 'edit', 'delete', 'manage'],
            'commandes' => ['view', 'create', 'edit', 'delete', 'validate'],
            'catalogues' => ['view', 'create', 'edit', 'delete'],
            'assurances' => ['view', 'create', 'edit', 'delete', 'manage'],
            'comptabilite' => ['view', 'create', 'edit', 'delete', 'validate'],
            'rapports' => ['view', 'create', 'export'],
            'notifications' => ['view', 'create', 'send'],
            'litiges' => ['view', 'create', 'edit', 'resolve'],
            'evaluations' => ['view', 'create', 'moderate'],
            'audit' => ['view', 'export'],
            'settings' => ['view', 'edit'],
        ];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                $permissionName = "{$module}.{$action}";
                $permissions[$permissionName] = Permission::create(['name' => $permissionName]);
            }
        }

        // Permissions spéciales
        $specialPermissions = [
            'dashboard.admin',
            'dashboard.medical',
            'dashboard.financial',
            'dashboard.patient',
            'teleconsultation.start',
            'teleconsultation.join',
            'emergency.access',
            'backup.create',
            'backup.restore',
            'system.maintain',
        ];

        foreach ($specialPermissions as $permission) {
            $permissions[$permission] = Permission::create(['name' => $permission]);
        }

        return $permissions;
    }

    /**
     * Créer les rôles et assigner les permissions
     */
    private function createRoles(array $permissions): void
    {
        // Super Admin - Toutes les permissions
        $superAdmin = Role::create(['name' => 'super-admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo([
            'dashboard.admin',
            'users.view', 'users.create', 'users.edit', 'users.verify',
            'structures.view', 'structures.create', 'structures.edit', 'structures.verify',
            'catalogues.view', 'catalogues.create', 'catalogues.edit',
            'assurances.view', 'assurances.create', 'assurances.edit',
            'rapports.view', 'rapports.create', 'rapports.export',
            'audit.view', 'audit.export',
            'settings.view', 'settings.edit',
            'litiges.view', 'litiges.edit', 'litiges.resolve',
            'evaluations.view', 'evaluations.moderate',
        ]);

        // Médecin
        $medecin = Role::create(['name' => 'medecin']);
        $medecin->givePermissionTo([
            'dashboard.medical',
            'consultations.view', 'consultations.create', 'consultations.edit',
            'ordonnances.view', 'ordonnances.create', 'ordonnances.edit',
            'rendez-vous.view', 'rendez-vous.create', 'rendez-vous.edit', 'rendez-vous.confirm',
            'dossiers-medicaux.view', 'dossiers-medicaux.create', 'dossiers-medicaux.edit',
            'factures.view', 'factures.create',
            'pec.view', 'pec.create',
            'teleconsultation.start',
            'emergency.access',
            'rapports.view',
            'notifications.view',
        ]);

        // Pharmacien
        $pharmacien = Role::create(['name' => 'pharmacien']);
        $pharmacien->givePermissionTo([
            'dashboard.medical',
            'ordonnances.view', 'ordonnances.dispense',
            'stocks.view', 'stocks.create', 'stocks.edit', 'stocks.manage',
            'commandes.view', 'commandes.create', 'commandes.edit', 'commandes.validate',
            'factures.view', 'factures.create',
            'paiements.view', 'paiements.create',
            'catalogues.view',
            'notifications.view', 'notifications.create',
        ]);

        // Infirmier
        $infirmier = Role::create(['name' => 'infirmier']);
        $infirmier->givePermissionTo([
            'dashboard.medical',
            'consultations.view', 'consultations.create',
            'rendez-vous.view', 'rendez-vous.create',
            'dossiers-medicaux.view', 'dossiers-medicaux.edit',
            'emergency.access',
            'notifications.view',
        ]);

        // Sage-femme
        $sageFemme = Role::create(['name' => 'sage-femme']);
        $sageFemme->givePermissionTo([
            'dashboard.medical',
            'consultations.view', 'consultations.create', 'consultations.edit',
            'ordonnances.view', 'ordonnances.create',
            'rendez-vous.view', 'rendez-vous.create', 'rendez-vous.edit',
            'dossiers-medicaux.view', 'dossiers-medicaux.create', 'dossiers-medicaux.edit',
            'factures.view', 'factures.create',
            'emergency.access',
        ]);

        // Dentiste
        $dentiste = Role::create(['name' => 'dentiste']);
        $dentiste->givePermissionTo([
            'dashboard.medical',
            'consultations.view', 'consultations.create', 'consultations.edit',
            'ordonnances.view', 'ordonnances.create',
            'rendez-vous.view', 'rendez-vous.create', 'rendez-vous.edit',
            'dossiers-medicaux.view', 'dossiers-medicaux.edit',
            'factures.view', 'factures.create',
        ]);

        // Biologiste
        $biologiste = Role::create(['name' => 'biologiste']);
        $biologiste->givePermissionTo([
            'dashboard.medical',
            'consultations.view',
            'dossiers-medicaux.view', 'dossiers-medicaux.edit',
            'factures.view', 'factures.create',
            'rapports.view', 'rapports.create',
        ]);

        // Patient
        $patient = Role::create(['name' => 'patient']);
        $patient->givePermissionTo([
            'dashboard.patient',
            'consultations.view', // Ses propres consultations
            'ordonnances.view', // Ses propres ordonnances
            'rendez-vous.view', 'rendez-vous.create', 'rendez-vous.edit',
            'dossiers-medicaux.view', 'dossiers-medicaux.share',
            'factures.view',
            'paiements.view', 'paiements.create',
            'commandes.view', 'commandes.create',
            'teleconsultation.join',
            'notifications.view',
            'evaluations.create',
            'litiges.create',
        ]);

        // Gestionnaire de structure
        $gestionnaire = Role::create(['name' => 'gestionnaire-structure']);
        $gestionnaire->givePermissionTo([
            'dashboard.admin',
            'structures.view', 'structures.edit',
            'users.view', 'users.create', 'users.edit', // Personnel de la structure
            'consultations.view',
            'factures.view',
            'reversements.view',
            'rapports.view', 'rapports.create', 'rapports.export',
            'catalogues.view', 'catalogues.edit',
            'evaluations.view',
        ]);

        // Comptable / Trésorier
        $comptable = Role::create(['name' => 'comptable']);
        $comptable->givePermissionTo([
            'dashboard.financial',
            'factures.view', 'factures.edit', 'factures.validate',
            'paiements.view', 'paiements.validate',
            'reversements.view', 'reversements.create', 'reversements.validate',
            'comptabilite.view', 'comptabilite.create', 'comptabilite.edit', 'comptabilite.validate',
            'rapports.view', 'rapports.create', 'rapports.export',
            'audit.view',
        ]);

        // Assureur
        $assureur = Role::create(['name' => 'assureur']);
        $assureur->givePermissionTo([
            'dashboard.financial',
            'pec.view', 'pec.edit', 'pec.approve',
            'factures.view',
            'assurances.view', 'assurances.edit', 'assurances.manage',
            'rapports.view', 'rapports.export',
            'litiges.view', 'litiges.edit',
        ]);

        // Administrateur assurance
        $adminAssurance = Role::create(['name' => 'admin-assurance']);
        $adminAssurance->givePermissionTo([
            'dashboard.financial',
            'pec.view', 'pec.create', 'pec.edit', 'pec.approve',
            'assurances.view', 'assurances.create', 'assurances.edit', 'assurances.manage',
            'factures.view',
            'paiements.view',
            'rapports.view', 'rapports.create', 'rapports.export',
            'litiges.view', 'litiges.edit', 'litiges.resolve',
        ]);

        // Livreur
        $livreur = Role::create(['name' => 'livreur']);
        $livreur->givePermissionTo([
            'commandes.view', 'commandes.edit',
            'notifications.view',
        ]);

        // Agent de caisse
        $agent = Role::create(['name' => 'agent-caisse']);
        $agent->givePermissionTo([
            'paiements.view', 'paiements.create',
            'factures.view',
            'notifications.view',
        ]);

        // Modérateur
        $moderateur = Role::create(['name' => 'moderateur']);
        $moderateur->givePermissionTo([
            'evaluations.view', 'evaluations.moderate',
            'litiges.view', 'litiges.edit',
            'notifications.view', 'notifications.create', 'notifications.send',
            'audit.view',
        ]);

        // Praticien générique (profil médical sans spécialité précise)
        $praticien = Role::create(['name' => 'praticien']);
        $praticien->givePermissionTo([
            'dashboard.medical',
            'consultations.view', 'consultations.create', 'consultations.edit',
            'ordonnances.view', 'ordonnances.create',
            'rendez-vous.view', 'rendez-vous.create', 'rendez-vous.edit',
            'dossiers-medicaux.view', 'dossiers-medicaux.edit',
            'factures.view', 'factures.create',
            'pec.view',
            'teleconsultation.start',
            'notifications.view',
        ]);
    }
}
