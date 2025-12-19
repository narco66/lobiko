<?php

namespace App\Policies;

use App\Models\Ordonnance;
use App\Models\User;

class OrdonnancePolicy
{
    public function view(User $user, Ordonnance $ordonnance): bool
    {
        return $user->id === $ordonnance->patient_id
            || $user->id === $ordonnance->prescripteur_id
            || $user->hasAnyRole(['super-admin', 'admin', 'pharmacien', 'medecin']);
    }

    public function update(User $user, Ordonnance $ordonnance): bool
    {
        return $user->id === $ordonnance->prescripteur_id
            || $user->hasAnyRole(['super-admin', 'admin']);
    }

    public function delete(User $user, Ordonnance $ordonnance): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']);
    }
}
