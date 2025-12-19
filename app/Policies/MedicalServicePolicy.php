<?php

namespace App\Policies;

use App\Models\MedicalService;
use App\Models\User;

class MedicalServicePolicy
{
    protected function canManage(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'super-admin', 'admin']) || $user->can('services.view');
    }

    public function viewAny(User $user): bool
    {
        return $this->canManage($user);
    }

    public function view(User $user, MedicalService $service): bool
    {
        return $this->canManage($user);
    }

    public function create(User $user): bool
    {
        return $user->can('services.create') || $this->canManage($user);
    }

    public function update(User $user, MedicalService $service): bool
    {
        return $user->can('services.update') || $this->canManage($user);
    }

    public function delete(User $user, MedicalService $service): bool
    {
        return $user->can('services.delete') || $this->canManage($user);
    }
}
