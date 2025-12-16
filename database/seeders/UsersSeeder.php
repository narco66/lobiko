<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin
        $superAdmin = User::create([
            'matricule' => 'LBK-2025-00001',
            'nom' => 'ADMIN',
            'prenom' => 'Super',
            'date_naissance' => '1980-01-01',
            'sexe' => 'M',
            'telephone' => '+241011111111',
            'email' => 'superadmin@lobiko.com',
            'email_verified_at' => now(),
            'password' => Hash::make('SuperAdmin@2025'),
            'adresse_rue' => 'Boulevard Triomphal',
            'adresse_quartier' => 'Centre Ville',
            'adresse_ville' => 'Libreville',
            'adresse_pays' => 'Gabon',
            'latitude' => 0.4162,
            'longitude' => 9.4673,
            'statut_compte' => 'actif',
            'langue_preferee' => 'fr',
            'photo_profil' => 'avatars/admin.jpg',
        ]);
        $superAdmin->assignRole('super-admin');

        // Admin
        $admin = User::create([
            'matricule' => 'LBK-2025-00002',
            'nom' => 'NZUE',
            'prenom' => 'Jean',
            'date_naissance' => '1985-03-15',
            'sexe' => 'M',
            'telephone' => '+241011111112',
            'email' => 'admin@lobiko.com',
            'email_verified_at' => now(),
            'password' => Hash::make('Admin@2025'),
            'adresse_rue' => 'Avenue du Colonel Parant',
            'adresse_quartier' => 'Glass',
            'adresse_ville' => 'Libreville',
            'adresse_pays' => 'Gabon',
            'latitude' => 0.3924,
            'longitude' => 9.4536,
            'statut_compte' => 'actif',
            'langue_preferee' => 'fr',
        ]);
        $admin->assignRole('admin');

        // Médecins
        $medecins = [
            [
                'nom' => 'MARTIN',
                'prenom' => 'Pierre',
                'email' => 'dr.martin@lobiko.com',
                'telephone' => '+241022222221',
                'specialite' => 'Médecine Générale',
                'numero_ordre' => 'OMG-2020-0145',
                'quartier' => 'Louis',
            ],
            [
                'nom' => 'OBAME',
                'prenom' => 'Marie',
                'email' => 'dr.obame@lobiko.com',
                'telephone' => '+241022222222',
                'specialite' => 'Pédiatrie',
                'numero_ordre' => 'OMG-2018-0089',
                'quartier' => 'Nombakélé',
            ],
            [
                'nom' => 'ESSONO',
                'prenom' => 'Paul',
                'email' => 'dr.essono@lobiko.com',
                'telephone' => '+241022222223',
                'specialite' => 'Cardiologie',
                'numero_ordre' => 'OMG-2015-0234',
                'quartier' => 'Batterie IV',
            ],
            [
                'nom' => 'MBA',
                'prenom' => 'Sophie',
                'email' => 'dr.mba@lobiko.com',
                'telephone' => '+241022222224',
                'specialite' => 'Gynécologie',
                'numero_ordre' => 'OMG-2019-0167',
                'quartier' => 'Montagne Sainte',
            ],
            [
                'nom' => 'NGUEMA',
                'prenom' => 'Albert',
                'email' => 'dr.nguema@lobiko.com',
                'telephone' => '+241022222225',
                'specialite' => 'Chirurgie',
                'numero_ordre' => 'OMG-2016-0298',
                'quartier' => 'Oloumi',
            ],
        ];

        foreach ($medecins as $data) {
            $medecin = User::create([
                'matricule' => User::generateMatricule((object)['role' => 'medecin']),
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'date_naissance' => fake()->dateTimeBetween('-55 years', '-30 years'),
                'sexe' => in_array($data['prenom'], ['Marie', 'Sophie']) ? 'F' : 'M',
                'telephone' => $data['telephone'],
                'email' => $data['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('Medecin@2025'),
                'adresse_rue' => fake()->streetName(),
                'adresse_quartier' => $data['quartier'],
                'adresse_ville' => 'Libreville',
                'adresse_pays' => 'Gabon',
                'latitude' => fake()->latitude(0.3, 0.5),
                'longitude' => fake()->longitude(9.4, 9.5),
                'specialite' => $data['specialite'],
                'numero_ordre' => $data['numero_ordre'],
                'certification_verified' => true,
                'certification_verified_at' => now()->subMonths(rand(1, 12)),
                'certification_verified_by' => $admin->id,
                'statut_compte' => 'actif',
                'langue_preferee' => 'fr',
                'note_moyenne' => fake()->randomFloat(1, 3.5, 5),
                'nombre_evaluations' => fake()->numberBetween(10, 100),
            ]);
            $medecin->assignRole('medecin');
        }

        // Pharmaciens
        $pharmaciens = [
            [
                'nom' => 'NDONG',
                'prenom' => 'Jeanne',
                'email' => 'pharmacie.centrale@lobiko.com',
                'telephone' => '+241033333331',
                'quartier' => 'Centre Ville',
            ],
            [
                'nom' => 'MOUSSAVOU',
                'prenom' => 'Eric',
                'email' => 'pharmacie.glass@lobiko.com',
                'telephone' => '+241033333332',
                'quartier' => 'Glass',
            ],
            [
                'nom' => 'BIYOGHE',
                'prenom' => 'Sylvie',
                'email' => 'pharmacie.oloumi@lobiko.com',
                'telephone' => '+241033333333',
                'quartier' => 'Oloumi',
            ],
        ];

        foreach ($pharmaciens as $data) {
            $pharmacien = User::create([
                'matricule' => User::generateMatricule((object)['role' => 'pharmacien']),
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'date_naissance' => fake()->dateTimeBetween('-50 years', '-30 years'),
                'sexe' => in_array($data['prenom'], ['Jeanne', 'Sylvie']) ? 'F' : 'M',
                'telephone' => $data['telephone'],
                'email' => $data['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('Pharmacie@2025'),
                'adresse_rue' => fake()->streetName(),
                'adresse_quartier' => $data['quartier'],
                'adresse_ville' => 'Libreville',
                'adresse_pays' => 'Gabon',
                'latitude' => fake()->latitude(0.3, 0.5),
                'longitude' => fake()->longitude(9.4, 9.5),
                'specialite' => 'Pharmacie',
                'numero_ordre' => 'OPG-' . fake()->year() . '-' . fake()->numberBetween(1000, 9999),
                'certification_verified' => true,
                'certification_verified_at' => now()->subMonths(rand(1, 12)),
                'certification_verified_by' => $admin->id,
                'statut_compte' => 'actif',
                'langue_preferee' => 'fr',
            ]);
            $pharmacien->assignRole('pharmacien');
        }

        // Infirmiers
        for ($i = 1; $i <= 5; $i++) {
            $infirmier = User::create([
                'matricule' => User::generateMatricule((object)['role' => 'infirmier']),
                'nom' => fake()->lastName(),
                'prenom' => fake()->firstName(),
                'date_naissance' => fake()->dateTimeBetween('-45 years', '-25 years'),
                'sexe' => fake()->randomElement(['M', 'F']),
                'telephone' => '+24104444' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'email' => 'infirmier' . $i . '@lobiko.com',
                'email_verified_at' => now(),
                'password' => Hash::make('Infirmier@2025'),
                'adresse_rue' => fake()->streetName(),
                'adresse_quartier' => fake()->randomElement(['Glass', 'Louis', 'Oloumi', 'Nombakélé']),
                'adresse_ville' => 'Libreville',
                'adresse_pays' => 'Gabon',
                'latitude' => fake()->latitude(0.3, 0.5),
                'longitude' => fake()->longitude(9.4, 9.5),
                'specialite' => 'Soins Infirmiers',
                'numero_ordre' => 'ONIG-' . fake()->year() . '-' . fake()->numberBetween(1000, 9999),
                'certification_verified' => true,
                'certification_verified_at' => now()->subMonths(rand(1, 12)),
                'certification_verified_by' => $admin->id,
                'statut_compte' => 'actif',
                'langue_preferee' => 'fr',
            ]);
            $infirmier->assignRole('infirmier');
        }

        // Patients
        $patients = [
            [
                'nom' => 'MEZUI',
                'prenom' => 'Test',
                'email' => 'patient.test@lobiko.com',
                'telephone' => '+241055555551',
            ],
            [
                'nom' => 'NTOUTOUME',
                'prenom' => 'Alice',
                'email' => 'alice.patient@lobiko.com',
                'telephone' => '+241055555552',
            ],
            [
                'nom' => 'OGANDAGA',
                'prenom' => 'Robert',
                'email' => 'robert.patient@lobiko.com',
                'telephone' => '+241055555553',
            ],
            [
                'nom' => 'MBADINGA',
                'prenom' => 'Fatou',
                'email' => 'fatou.patient@lobiko.com',
                'telephone' => '+241055555554',
            ],
            [
                'nom' => 'EDOU',
                'prenom' => 'Charles',
                'email' => 'charles.patient@lobiko.com',
                'telephone' => '+241055555555',
            ],
        ];

        foreach ($patients as $data) {
            $patient = User::create([
                'matricule' => User::generateMatricule((object)['role' => 'patient']),
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'date_naissance' => fake()->dateTimeBetween('-60 years', '-18 years'),
                'sexe' => in_array($data['prenom'], ['Alice', 'Fatou']) ? 'F' : 'M',
                'telephone' => $data['telephone'],
                'email' => $data['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('Patient@2025'),
                'adresse_rue' => fake()->streetName(),
                'adresse_quartier' => fake()->randomElement(['Glass', 'Louis', 'Oloumi', 'Nombakélé', 'Batterie IV']),
                'adresse_ville' => 'Libreville',
                'adresse_pays' => 'Gabon',
                'latitude' => fake()->latitude(0.3, 0.5),
                'longitude' => fake()->longitude(9.4, 9.5),
                'statut_compte' => 'actif',
                'langue_preferee' => 'fr',
                'piece_identite_type' => 'CNI',
                'piece_identite_numero' => fake()->numerify('##########'),
            ]);
            $patient->assignRole('patient');
        }

        // Créer plus de patients pour les tests
        for ($i = 1; $i <= 20; $i++) {
            $patient = User::create([
                'matricule' => User::generateMatricule((object)['role' => 'patient']),
                'nom' => fake()->lastName(),
                'prenom' => fake()->firstName(),
                'date_naissance' => fake()->dateTimeBetween('-70 years', '-18 years'),
                'sexe' => fake()->randomElement(['M', 'F']),
                'telephone' => '+24106666' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'email' => 'patient' . ($i + 5) . '@lobiko.com',
                'email_verified_at' => fake()->optional(0.8)->dateTimeBetween('-1 year', 'now'),
                'password' => Hash::make('Patient@2025'),
                'adresse_rue' => fake()->streetName(),
                'adresse_quartier' => fake()->randomElement(['Glass', 'Louis', 'Oloumi', 'Nombakélé', 'Batterie IV', 'Montagne Sainte']),
                'adresse_ville' => fake()->randomElement(['Libreville', 'Port-Gentil', 'Franceville', 'Oyem']),
                'adresse_pays' => 'Gabon',
                'latitude' => fake()->latitude(0.3, 0.5),
                'longitude' => fake()->longitude(9.4, 9.5),
                'statut_compte' => fake()->randomElement(['actif', 'actif', 'actif', 'suspendu']),
                'langue_preferee' => 'fr',
            ]);
            $patient->assignRole('patient');
        }

        // Comptable
        $comptable = User::create([
            'matricule' => 'LBK-2025-00100',
            'nom' => 'MOUNGUENGUI',
            'prenom' => 'François',
            'date_naissance' => '1982-07-20',
            'sexe' => 'M',
            'telephone' => '+241077777771',
            'email' => 'comptable@lobiko.com',
            'email_verified_at' => now(),
            'password' => Hash::make('Comptable@2025'),
            'adresse_rue' => 'Boulevard de l\'Indépendance',
            'adresse_quartier' => 'Centre Ville',
            'adresse_ville' => 'Libreville',
            'adresse_pays' => 'Gabon',
            'latitude' => 0.4162,
            'longitude' => 9.4673,
            'statut_compte' => 'actif',
            'langue_preferee' => 'fr',
        ]);
        $comptable->assignRole('comptable');

        // Assureur
        $assureur = User::create([
            'matricule' => 'LBK-2025-00200',
            'nom' => 'BEKALE',
            'prenom' => 'Henriette',
            'date_naissance' => '1979-11-10',
            'sexe' => 'F',
            'telephone' => '+241088888881',
            'email' => 'assurance@lobiko.com',
            'email_verified_at' => now(),
            'password' => Hash::make('Assurance@2025'),
            'adresse_rue' => 'Avenue Bouët',
            'adresse_quartier' => 'Glass',
            'adresse_ville' => 'Libreville',
            'adresse_pays' => 'Gabon',
            'latitude' => 0.3924,
            'longitude' => 9.4536,
            'statut_compte' => 'actif',
            'langue_preferee' => 'fr',
        ]);
        $assureur->assignRole('assureur');

        // Livreurs
        for ($i = 1; $i <= 3; $i++) {
            $livreur = User::create([
                'matricule' => User::generateMatricule((object)['role' => 'livreur']),
                'nom' => fake()->lastName(),
                'prenom' => fake()->firstName('male'),
                'date_naissance' => fake()->dateTimeBetween('-35 years', '-20 years'),
                'sexe' => 'M',
                'telephone' => '+24109999' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'email' => 'livreur' . $i . '@lobiko.com',
                'email_verified_at' => now(),
                'password' => Hash::make('Livreur@2025'),
                'adresse_rue' => fake()->streetName(),
                'adresse_quartier' => fake()->randomElement(['PK8', 'PK9', 'PK10']),
                'adresse_ville' => 'Libreville',
                'adresse_pays' => 'Gabon',
                'latitude' => fake()->latitude(0.3, 0.5),
                'longitude' => fake()->longitude(9.4, 9.5),
                'statut_compte' => 'actif',
                'langue_preferee' => 'fr',
            ]);
            $livreur->assignRole('livreur');
        }

        $this->command->info('Utilisateurs créés avec succès!');
    }
}
