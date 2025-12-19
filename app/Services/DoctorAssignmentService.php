<?php

namespace App\Services;

use App\Models\Doctor;
use App\Models\MedicalStructure;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DoctorAssignmentService
{
    /**
     * Assigne un médecin à une structure avec rôle et pourcentage.
     */
    public function assign(Doctor $doctor, MedicalStructure $structure, array $pivotData = []): void
    {
        DB::transaction(function () use ($doctor, $structure, $pivotData) {
            $existing = $doctor->structures()->where('structures_medicales.id', $structure->id)->first();
            if ($existing) {
                throw ValidationException::withMessages([
                    'structures' => 'Le médecin est déjà affecté à cette structure.'
                ]);
            }

            $doctor->structures()->attach($structure->id, [
                'role' => $pivotData['role'] ?? 'praticien',
                'actif' => $pivotData['actif'] ?? true,
                'date_debut' => $pivotData['date_debut'] ?? now()->toDateString(),
                'date_fin' => $pivotData['date_fin'] ?? null,
                'pourcentage_honoraires' => $pivotData['pourcentage_honoraires'] ?? 70,
            ]);
        });
    }

    public function detach(Doctor $doctor, MedicalStructure $structure): void
    {
        $doctor->structures()->detach($structure->id);
    }
}
