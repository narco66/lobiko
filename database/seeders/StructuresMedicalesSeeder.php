<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\StructureMedicale;
use App\Models\User;

class StructuresMedicalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = ['cabinet', 'clinique', 'hopital', 'pharmacie', 'laboratoire'];

        // Créer quelques structures si aucune n'existe
        if (StructureMedicale::count() === 0) {
            foreach ($types as $type) {
                StructureMedicale::create([
                    'id' => Str::uuid(),
                    'code_structure' => strtoupper(substr($type, 0, 3)) . '-' . rand(1000, 9999),
                    'nom_structure' => ucfirst($type) . ' Démo',
                    'type_structure' => $type,
                    'type' => $type,
                    'numero_agrement' => 'AGR-' . rand(10000, 99999),
                    'numero_fiscal' => 'NIF-' . rand(100000, 999999),
                    'registre_commerce' => 'RC-' . rand(100000, 999999),
                    'adresse_rue' => 'Rue de la Santé',
                    'adresse_quartier' => 'Centre',
                    'adresse_ville' => 'Libreville',
                    'adresse_pays' => 'Gabon',
                    'latitude' => 0.3901,
                    'longitude' => 9.4544,
                    'telephone_principal' => '+24101234567',
                    'telephone_secondaire' => null,
                    'email' => 'contact@' . $type . '.demo',
                    'site_web' => null,
                    'horaires_ouverture' => json_encode(['lun-ven' => '08:00-18:00']),
                    'urgences_24h' => $type === 'hopital',
                    'garde_weekend' => in_array($type, ['clinique', 'hopital']),
                    'responsable_id' => User::query()->first()?->id,
                    'services_disponibles' => json_encode(['consultation', 'urgence']),
                    'equipements' => json_encode(['radio', 'echo']),
                    'nombre_lits' => $type === 'hopital' ? 50 : 0,
                    'nombre_salles' => 5,
                    'parking_disponible' => true,
                    'accessible_handicapes' => true,
                    'assurances_acceptees' => json_encode([]),
                    'tiers_payant' => true,
                    'categorie_tarif' => 'prive',
                    'taux_majoration' => 0,
                    'statut' => 'actif',
                    'verified' => true,
                    'verified_at' => now(),
                    'verified_by' => User::query()->first()?->id,
                    'logo' => null,
                    'photo_facade' => null,
                    'galerie_photos' => json_encode([]),
                    'document_agrement' => null,
                    'note_moyenne' => 4.5,
                    'nombre_evaluations' => 10,
                    'nombre_consultations' => 100,
                    'compte_bancaire' => null,
                    'code_banque' => null,
                    'iban' => null,
                    'commission_plateforme' => 10,
                ]);
            }
        }
    }
}
