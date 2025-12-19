<?php

namespace App\Policies;

use App\Models\Partner;
use App\Models\User;

class PartnerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']) || $user->can('partners.view');
    }

    public function view(User $user, Partner $partner): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']) || $user->can('partners.create');
    }

    public function update(User $user, Partner $partner): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']) || $user->can('partners.update');
    }

    public function delete(User $user, Partner $partner): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']) || $user->can('partners.delete');
    }
}
