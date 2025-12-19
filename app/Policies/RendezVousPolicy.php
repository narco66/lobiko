<?php

namespace App\Policies;

use App\Models\RendezVous;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RendezVousPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin', 'medecin', 'patient']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, RendezVous $rendezVous): bool
    {
        if ($user->hasAnyRole(['admin', 'super-admin'])) {
            return true;
        }
        if ($user->id === (string) $rendezVous->patient_id) {
            return true;
        }
        if ($user->id === (string) $rendezVous->professionnel_id) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin', 'patient', 'medecin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, RendezVous $rendezVous): bool
    {
        if ($user->hasAnyRole(['admin', 'super-admin'])) {
            return true;
        }
        if ($user->id === (string) $rendezVous->patient_id) {
            return true;
        }
        if ($user->id === (string) $rendezVous->professionnel_id) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RendezVous $rendezVous): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, RendezVous $rendezVous): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, RendezVous $rendezVous): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }
}
