<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\MedicalService;
use App\Models\MedicalStructure;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MedicalReferentialsSeeder extends Seeder
{
    public function run(): void
    {
        // S'assurer qu'un responsable existe pour satisfaire la FK non nulle
        $responsable = User::firstOrCreate(
            ['email' => 'admin@lobiko.com'],
            [
                'nom' => 'Admin',
                'prenom' => 'Lobiko',
                'password' => Hash::make('Admin@2025'),
                'date_naissance' => '1980-01-01',
                'sexe' => 'M',
                'telephone' => '+24101000000',
                'statut_compte' => 'actif',
                'email_verified_at' => now(),
                'adresse_ville' => 'Libreville',
                'adresse_pays' => 'Gabon',
            ]
        );

        $specialties = [
            ['code' => 'CARD', 'libelle' => 'Cardiologie'],
            ['code' => 'PED', 'libelle' => 'Pédiatrie'],
            ['code' => 'GEN', 'libelle' => 'Médecine générale'],
        ];
        foreach ($specialties as $spec) {
            Specialty::firstOrCreate(['code' => $spec['code']], $spec);
        }

        $services = [
            ['code' => 'IMAG', 'libelle' => 'Imagerie'],
            ['code' => 'LAB', 'libelle' => 'Laboratoire'],
        ];
        foreach ($services as $srv) {
            MedicalService::firstOrCreate(['code' => $srv['code']], $srv);
        }

        $structure = MedicalStructure::firstOrCreate(
            ['code_structure' => 'STR-001'],
            [
                'id' => (string) Str::uuid(),
                'nom_structure' => 'Clinique Centrale',
                'type_structure' => 'clinique',
                'adresse_rue' => 'Boulevard Triomphal',
                'adresse_quartier' => 'Centre-ville',
                'adresse_ville' => 'Libreville',
                'adresse_pays' => 'Gabon',
                'latitude' => 0.3900,
                'longitude' => 9.4536,
                'telephone_principal' => '+24101000000',
                'email' => 'contact@clinique.local',
                'statut' => 'actif',
                'responsable_id' => $responsable->id,
                'horaires_ouverture' => [],
            ]
        );

        $spec = Specialty::where('code', 'GEN')->first();
        if ($spec && Doctor::count() === 0) {
            Doctor::create([
                'user_id' => $responsable->id,
                'matricule' => 'DOC-0001',
                'nom' => 'Demo',
                'prenom' => 'Medecin',
                'specialty_id' => $spec->id,
                'statut' => 'actif',
                'verified' => true,
            ]);
        }
    }
}
