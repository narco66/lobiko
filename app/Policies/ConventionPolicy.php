<?php

namespace App\Policies;

use App\Models\Convention;
use App\Models\User;

class ConventionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']) || $user->can('conventions.view');
    }

    public function view(User $user, Convention $convention): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']) || $user->can('conventions.create');
    }

    public function update(User $user, Convention $convention): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']) || $user->can('conventions.update');
    }

    public function approve(User $user, Convention $convention): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']) || $user->can('conventions.approve');
    }
}
