<?php

namespace App\Policies;

use App\Models\GrilleTarifaire;
use App\Models\User;

class GrilleTarifairePolicy
{
    protected function canManage(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'super-admin', 'admin'])
            || $user->can('grilles.view');
    }

    public function viewAny(User $user): bool
    {
        return $this->canManage($user);
    }

    public function view(User $user, GrilleTarifaire $grille): bool
    {
        return $this->canManage($user);
    }

    public function create(User $user): bool
    {
        return $user->can('grilles.create') || $this->canManage($user);
    }

    public function update(User $user, GrilleTarifaire $grille): bool
    {
        return $user->can('grilles.update') || $this->canManage($user);
    }

    public function delete(User $user, GrilleTarifaire $grille): bool
    {
        return $user->can('grilles.delete') || $this->canManage($user);
    }
}
