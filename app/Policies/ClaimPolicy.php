<?php

namespace App\Policies;

use App\Models\Claim;
use App\Models\User;

class ClaimPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']) || $user->can('claims.view');
    }

    public function view(User $user, Claim $claim): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']) || $user->can('claims.create');
    }

    public function approve(User $user, Claim $claim): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']) || $user->can('claims.approve');
    }

    public function pay(User $user, Claim $claim): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']) || $user->can('claims.pay');
    }
}
