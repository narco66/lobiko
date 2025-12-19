<?php

namespace Database\Seeders;

use App\Models\Pharmacie;
use App\Models\StockMedicament;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestsBaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        $accounts = [
            ['role' => 'admin', 'email' => 'admin@lobiko.com', 'password' => 'Admin@2025', 'nom' => 'Admin', 'prenom' => 'Lobiko'],
            ['role' => 'medecin', 'email' => 'dr.martin@lobiko.com', 'password' => 'Medecin@2025', 'nom' => 'Martin', 'prenom' => 'Docteur', 'factory' => 'medecin'],
            ['role' => 'patient', 'email' => 'patient.test@lobiko.com', 'password' => 'Patient@2025', 'nom' => 'Patient', 'prenom' => 'Test'],
            ['role' => 'pharmacien', 'email' => 'pharmacie.centrale@lobiko.com', 'password' => 'Pharmacie@2025', 'nom' => 'Pharmacie', 'prenom' => 'Centrale', 'factory' => 'pharmacien'],
            ['role' => 'assureur', 'email' => 'assurance@lobiko.com', 'password' => 'Assurance@2025', 'nom' => 'Assurance', 'prenom' => 'Lobiko'],
            ['role' => 'comptable', 'email' => 'comptable@lobiko.com', 'password' => 'Comptable@2025', 'nom' => 'Comptable', 'prenom' => 'Lobiko'],
        ];

        foreach ($accounts as $data) {
            $attrs = [
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'password' => Hash::make($data['password']),
                'adresse_ville' => 'Libreville',
                'adresse_pays' => 'Gabon',
                'statut_compte' => 'actif',
            ];
            if (($data['factory'] ?? null) === 'medecin') {
                $attrs = array_merge($attrs, User::factory()->medecin()->raw());
            } elseif (($data['factory'] ?? null) === 'pharmacien') {
                $attrs = array_merge($attrs, User::factory()->pharmacien()->raw());
            }
            $attrs['password'] = Hash::make($data['password']);
            $attrs['email'] = $data['email'];

            $user = User::firstOrCreate(['email' => $data['email']], $attrs);
            $user->syncRoles([$data['role']]);
        }

        // Pharmacie + stock minimal
        $pharmacie = Pharmacie::factory()->create([
            'latitude' => 0.0,
            'longitude' => 0.0,
        ]);
        StockMedicament::factory()->create([
            'pharmacie_id' => $pharmacie->id,
            'quantite_disponible' => 20,
        ]);
    }
}
