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
                $permissions[$permissionName] = Permission::firstOrCreate(['name' => $permissionName]);
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
            $permissions[$permission] = Permission::firstOrCreate(['name' => $permission]);
        }

        return $permissions;
    }

    /**
     * Créer les rôles et assigner les permissions
     */
    private function createRoles(array $permissions): void
    {
        $sync = function (string $name, array $perms) {
            $role = Role::firstOrCreate(['name' => $name]);
            $role->syncPermissions($perms);
        };

        $sync('super-admin', Permission::all()->pluck('name')->toArray());

        $sync('admin', [
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

        $sync('medecin', [
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

        $sync('pharmacien', [
            'dashboard.medical',
            'ordonnances.view', 'ordonnances.dispense',
            'stocks.view', 'stocks.create', 'stocks.edit', 'stocks.manage',
            'commandes.view', 'commandes.create', 'commandes.edit', 'commandes.validate',
            'factures.view', 'factures.create',
            'paiements.view', 'paiements.create',
            'catalogues.view',
            'notifications.view', 'notifications.create',
        ]);

        $sync('infirmier', [
            'dashboard.medical',
            'consultations.view', 'consultations.create',
            'rendez-vous.view', 'rendez-vous.create',
            'dossiers-medicaux.view', 'dossiers-medicaux.edit',
            'emergency.access',
            'notifications.view',
        ]);

        $sync('sage-femme', [
            'dashboard.medical',
            'consultations.view', 'consultations.create', 'consultations.edit',
            'ordonnances.view', 'ordonnances.create',
            'rendez-vous.view', 'rendez-vous.create', 'rendez-vous.edit',
            'dossiers-medicaux.view', 'dossiers-medicaux.create', 'dossiers-medicaux.edit',
            'factures.view', 'factures.create',
            'emergency.access',
        ]);

        $sync('dentiste', [
            'dashboard.medical',
            'consultations.view', 'consultations.create', 'consultations.edit',
            'ordonnances.view', 'ordonnances.create',
            'rendez-vous.view', 'rendez-vous.create', 'rendez-vous.edit',
            'dossiers-medicaux.view', 'dossiers-medicaux.edit',
            'factures.view', 'factures.create',
        ]);

        $sync('biologiste', [
            'dashboard.medical',
            'consultations.view',
            'dossiers-medicaux.view', 'dossiers-medicaux.edit',
            'factures.view', 'factures.create',
            'rapports.view', 'rapports.create',
        ]);

        $sync('patient', [
            'dashboard.patient',
            'consultations.view',
            'ordonnances.view',
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

        $sync('assureur', [
            'dashboard.financial',
            'assurances.view', 'assurances.create', 'assurances.edit', 'assurances.manage',
            'pec.view', 'pec.approve',
            'factures.view', 'factures.validate',
            'paiements.view', 'paiements.validate',
            'reversements.view', 'reversements.validate',
            'rapports.view', 'rapports.export',
        ]);

        $sync('comptable', [
            'dashboard.financial',
            'factures.view', 'factures.create', 'factures.edit', 'factures.validate',
            'paiements.view', 'paiements.validate',
            'reversements.view', 'reversements.validate',
            'comptabilite.view', 'comptabilite.create', 'comptabilite.edit', 'comptabilite.validate',
            'rapports.view', 'rapports.export',
        ]);

        $sync('livreur', [
            'dashboard.medical',
            'commandes.view',
            'rendez-vous.view',
            'paiements.view',
        ]);

        $sync('moderateur', [
            'evaluations.view', 'evaluations.moderate',
        ]);

        $sync('praticien', [
            'dashboard.medical',
            'consultations.view', 'consultations.create',
            'rendez-vous.view', 'rendez-vous.create',
            'dossiers-medicaux.view', 'dossiers-medicaux.edit',
            'teleconsultation.start',
            'rapports.view',
        ]);
    }
}

