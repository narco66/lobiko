<?php

namespace App\Policies;

use App\Models\Consultation;
use App\Models\User;

class ConsultationPolicy
{
    public function view(User $user, Consultation $consultation): bool
    {
        if ($user->id === (string) $consultation->patient_id) {
            return true;
        }

        if ($user->id === (string) $consultation->professionnel_id) {
            return true;
        }

        // Accès rôle : supervision uniquement (admin / super-admin)
        return $user->hasAnyRole(['super-admin', 'admin']);
    }

    public function update(User $user, Consultation $consultation): bool
    {
        if ($user->id === (string) $consultation->professionnel_id) {
            return true;
        }

        return $user->hasAnyRole(['super-admin', 'admin']);
    }
}
