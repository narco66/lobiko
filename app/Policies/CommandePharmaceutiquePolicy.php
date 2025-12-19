<?php

namespace App\Policies;

use App\Models\CommandePharmaceutique;
use App\Models\User;

class CommandePharmaceutiquePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'pharmacien', 'patient']);
    }

    public function view(User $user, CommandePharmaceutique $commande): bool
    {
        return $this->isOwner($user, $commande) || $this->isPharmacienDeLaCommande($user, $commande) || $this->isAdmin($user);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('patient') || $user->hasAnyRole(['admin', 'pharmacien']);
    }

    public function update(User $user, CommandePharmaceutique $commande): bool
    {
        // Patient peut mettre à jour sa commande (ex: annulation), pharmacien et admin peuvent gérer le flux.
        return $this->isOwner($user, $commande) || $this->isPharmacienDeLaCommande($user, $commande) || $this->isAdmin($user);
    }

    public function delete(User $user, CommandePharmaceutique $commande): bool
    {
        return $this->isAdmin($user);
    }

    protected function isOwner(User $user, CommandePharmaceutique $commande): bool
    {
        return $user->id === $commande->patient_id;
    }

    protected function isPharmacienDeLaCommande(User $user, CommandePharmaceutique $commande): bool
    {
        if (!$user->hasRole('pharmacien')) {
            return false;
        }

        return $user->structure_medicale_id && $commande->pharmacie && $commande->pharmacie->structure_medicale_id === $user->structure_medicale_id;
    }

    protected function isAdmin(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }
}
