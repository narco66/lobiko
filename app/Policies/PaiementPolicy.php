<?php

namespace App\Policies;

use App\Models\Paiement;
use App\Models\User;

class PaiementPolicy
{
    protected function canManage(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'super-admin', 'admin']) || $user->can('payments.view');
    }

    public function viewAny(User $user): bool
    {
        return $this->canManage($user);
    }

    public function view(User $user, Paiement $paiement): bool
    {
        return $this->canManage($user);
    }
}
