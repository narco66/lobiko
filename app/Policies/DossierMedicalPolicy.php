<?php

namespace App\Policies;

use App\Models\DossierMedical;
use App\Models\User;

class DossierMedicalPolicy
{
    public function view(User $user, DossierMedical $dossier): bool
    {
        if ($user->id === (string) $dossier->patient_id) {
            return true;
        }

        // Accès supervision uniquement
        return $user->hasAnyRole(['super-admin', 'admin']);
    }

    public function update(User $user, DossierMedical $dossier): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']);
    }
}
