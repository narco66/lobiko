<?php

namespace App\Policies;

use App\Models\Specialty;
use App\Models\User;

class SpecialtyPolicy
{
    protected function canManage(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'super-admin', 'admin']) || $user->can('specialties.view');
    }

    public function viewAny(User $user): bool
    {
        return $this->canManage($user);
    }

    public function view(User $user, Specialty $specialty): bool
    {
        return $this->canManage($user);
    }

    public function create(User $user): bool
    {
        return $user->can('specialties.create') || $this->canManage($user);
    }

    public function update(User $user, Specialty $specialty): bool
    {
        return $user->can('specialties.update') || $this->canManage($user);
    }

    public function delete(User $user, Specialty $specialty): bool
    {
        return $user->can('specialties.delete') || $this->canManage($user);
    }
}
