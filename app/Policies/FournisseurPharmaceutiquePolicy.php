<?php

namespace App\Policies;

use App\Models\FournisseurPharmaceutique;
use App\Models\User;

class FournisseurPharmaceutiquePolicy
{
    protected function canManage(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'super-admin', 'admin'])
            || $user->can('fournisseurs.view');
    }

    public function viewAny(User $user): bool
    {
        return $this->canManage($user);
    }

    public function view(User $user, FournisseurPharmaceutique $fournisseur): bool
    {
        return $this->canManage($user);
    }

    public function create(User $user): bool
    {
        return $user->can('fournisseurs.create') || $this->canManage($user);
    }

    public function update(User $user, FournisseurPharmaceutique $fournisseur): bool
    {
        return $user->can('fournisseurs.update') || $this->canManage($user);
    }

    public function delete(User $user, FournisseurPharmaceutique $fournisseur): bool
    {
        return $user->can('fournisseurs.delete') || $this->canManage($user);
    }
}
