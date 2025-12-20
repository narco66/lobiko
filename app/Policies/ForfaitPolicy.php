<?php

namespace App\Policies;

use App\Models\Forfait;
use App\Models\User;

class ForfaitPolicy
{
    protected function canManage(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'super-admin', 'admin'])
            || $user->can('forfaits.view');
    }

    public function viewAny(User $user): bool
    {
        return $this->canManage($user);
    }

    public function view(User $user, Forfait $forfait): bool
    {
        return $this->canManage($user);
    }

    public function create(User $user): bool
    {
        return $user->can('forfaits.create') || $this->canManage($user);
    }

    public function update(User $user, Forfait $forfait): bool
    {
        return $user->can('forfaits.update') || $this->canManage($user);
    }

    public function delete(User $user, Forfait $forfait): bool
    {
        return $user->can('forfaits.delete') || $this->canManage($user);
    }
}
