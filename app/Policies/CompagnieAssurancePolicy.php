<?php

namespace App\Policies;

use App\Models\CompagnieAssurance;
use App\Models\User;

class CompagnieAssurancePolicy
{
    protected function canManage(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'super-admin', 'admin'])
            || $user->can('assurances.view');
    }

    public function viewAny(User $user): bool
    {
        return $this->canManage($user);
    }

    public function view(User $user, CompagnieAssurance $assurance): bool
    {
        return $this->canManage($user);
    }

    public function create(User $user): bool
    {
        return $user->can('assurances.create') || $this->canManage($user);
    }

    public function update(User $user, CompagnieAssurance $assurance): bool
    {
        return $user->can('assurances.update') || $this->canManage($user);
    }

    public function delete(User $user, CompagnieAssurance $assurance): bool
    {
        return $user->can('assurances.delete') || $this->canManage($user);
    }
}
