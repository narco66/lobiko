<?php

namespace App\Policies;

use App\Models\ProduitPharmaceutique;
use App\Models\User;

class ProduitPharmaceutiquePolicy
{
    protected function canManage(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'super-admin', 'admin'])
            || $user->can('produits.view');
    }

    public function viewAny(User $user): bool
    {
        return $this->canManage($user);
    }

    public function view(User $user, ProduitPharmaceutique $produit): bool
    {
        return $this->canManage($user);
    }

    public function create(User $user): bool
    {
        return $user->can('produits.create') || $this->canManage($user);
    }

    public function update(User $user, ProduitPharmaceutique $produit): bool
    {
        return $user->can('produits.update') || $this->canManage($user);
    }

    public function delete(User $user, ProduitPharmaceutique $produit): bool
    {
        return $user->can('produits.delete') || $this->canManage($user);
    }
}
