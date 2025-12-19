<?php

namespace App\Policies;

use App\Models\MedicalStructure;
use App\Models\User;

class MedicalStructurePolicy
{
    protected function canManage(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'super-admin', 'admin']) || $user->can('structures.view');
    }

    public function viewAny(User $user): bool
    {
        return $this->canManage($user);
    }

    public function view(User $user, MedicalStructure $structure): bool
    {
        return $this->canManage($user);
    }

    public function create(User $user): bool
    {
        return $user->can('structures.create') || $this->canManage($user);
    }

    public function update(User $user, MedicalStructure $structure): bool
    {
        return $user->can('structures.update') || $this->canManage($user);
    }

    public function delete(User $user, MedicalStructure $structure): bool
    {
        return $user->can('structures.delete') || $this->canManage($user);
    }
}
