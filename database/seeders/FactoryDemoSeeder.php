<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\StructureMedicale;
use App\Models\RendezVous;
use App\Models\Consultation;

class FactoryDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Créer rôles essentiels si non présents
        if (method_exists(app(User::class), 'assignRole')) {
            app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        }

        $patients = User::factory()->count(20)->patient()->create();
        $medecins = User::factory()->count(10)->medecin()->create();
        $structures = StructureMedicale::factory()->count(5)->create();

        // Associer praticiens aux structures
        foreach ($medecins as $doc) {
            $structures->random()->personnel()->attach($doc->id, ['role' => 'praticien', 'actif' => true]);
        }

        // Rendez-vous + consultations
        RendezVous::factory()
            ->count(15)
            ->state(function () use ($patients, $medecins, $structures) {
                return [
                    'patient_id' => $patients->random()->id,
                    'professionnel_id' => $medecins->random()->id,
                    'structure_id' => $structures->random()->id,
                ];
            })
            ->create()
            ->each(function ($rdv) {
                Consultation::factory()->create([
                    'patient_id' => $rdv->patient_id,
                    'professionnel_id' => $rdv->professionnel_id,
                    'structure_id' => $rdv->structure_id,
                    'date_consultation' => $rdv->date_heure,
                    'modalite' => $rdv->modalite,
                ]);
            });
    }
}
