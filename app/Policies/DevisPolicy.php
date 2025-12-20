<?php

namespace App\Policies;

use App\Models\Devis;
use App\Models\User;

class DevisPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'comptable', 'medecin']);
    }

    public function view(User $user, Devis $devis): bool
    {
        return $user->id === $devis->patient_id
            || $user->id === $devis->praticien_id
            || $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'comptable', 'medecin']);
    }

    public function update(User $user, Devis $devis): bool
    {
        return $user->id === $devis->praticien_id
            || $user->hasAnyRole(['super-admin', 'admin', 'comptable']);
    }

    public function delete(User $user, Devis $devis): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'comptable']);
    }
}
