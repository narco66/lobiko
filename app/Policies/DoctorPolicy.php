<?php

namespace App\Policies;

use App\Models\Doctor;
use App\Models\User;

class DoctorPolicy
{
    protected function canManage(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'super-admin', 'admin']) || $user->can('doctors.view');
    }

    public function viewAny(User $user): bool
    {
        return $this->canManage($user);
    }

    public function view(User $user, Doctor $doctor): bool
    {
        return $this->canManage($user);
    }

    public function create(User $user): bool
    {
        return $user->can('doctors.create') || $this->canManage($user);
    }

    public function update(User $user, Doctor $doctor): bool
    {
        return $user->can('doctors.update') || $this->canManage($user);
    }

    public function delete(User $user, Doctor $doctor): bool
    {
        return $user->can('doctors.delete') || $this->canManage($user);
    }
}
