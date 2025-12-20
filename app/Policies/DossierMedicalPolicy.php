<?php

namespace App\Policies;

use App\Models\DossierMedical;
use App\Models\User;

class DossierMedicalPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'medecin']);
    }

    public function view(User $user, DossierMedical $dossier): bool
    {
        if ($user->id === (string) $dossier->patient_id) {
            return true;
        }

        return $user->hasAnyRole(['super-admin', 'admin', 'medecin']);
    }

    public function update(User $user, DossierMedical $dossier): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'medecin']);
    }

    public function delete(User $user, DossierMedical $dossier): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']);
    }
}
