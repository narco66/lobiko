<?php

namespace App\Policies;

use App\Models\Facture;
use App\Models\User;

class FacturePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'comptable', 'medecin']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'comptable', 'medecin']);
    }

    public function view(User $user, Facture $facture): bool
    {
        return $user->id === $facture->patient_id
            || $user->id === $facture->praticien_id
            || $user->hasAnyRole(['super-admin', 'admin', 'medecin', 'comptable']);
    }

    public function update(User $user, Facture $facture): bool
    {
        return $user->id === $facture->praticien_id
            || $user->hasAnyRole(['super-admin', 'admin', 'comptable']);
    }

    public function delete(User $user, Facture $facture): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'comptable']);
    }
}
