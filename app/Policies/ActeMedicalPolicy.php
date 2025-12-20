<?php

namespace App\Policies;

use App\Models\ActeMedical;
use App\Models\User;

class ActeMedicalPolicy
{
    protected function canManage(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'super-admin', 'admin'])
            || $user->can('actes.view');
    }

    public function viewAny(User $user): bool
    {
        return $this->canManage($user);
    }

    public function view(User $user, ActeMedical $acte): bool
    {
        return $this->canManage($user);
    }

    public function create(User $user): bool
    {
        return $user->can('actes.create') || $this->canManage($user);
    }

    public function update(User $user, ActeMedical $acte): bool
    {
        return $user->can('actes.update') || $this->canManage($user);
    }

    public function delete(User $user, ActeMedical $acte): bool
    {
        return $user->can('actes.delete') || $this->canManage($user);
    }
}
