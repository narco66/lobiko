<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // S'assure que le rôle existe
        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $super = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);

        $data = [
            'nom' => 'Admin',
            'prenom' => 'Super',
            'date_naissance' => '1980-01-01',
            'sexe' => 'M',
            'telephone' => '0700000000',
            'adresse_rue' => 'Siège',
            'adresse_ville' => 'Libreville',
            'adresse_pays' => 'Gabon',
            'password' => Hash::make('Admin@2025'),
            'email_verified_at' => now(),
            'statut_compte' => 'actif',
        ];

        $user = User::withTrashed()->updateOrCreate(
            ['email' => 'admin@lobiko.com'],
            $data
        );
        if ($user->trashed()) {
            $user->restore();
        }

        if (!$user->hasRole($role)) {
            $user->assignRole($role);
        }
        if (!$user->hasRole($super)) {
            $user->assignRole($super);
        }
    }
}
