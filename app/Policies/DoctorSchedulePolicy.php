<?php

namespace App\Policies;

use App\Models\DoctorSchedule;
use App\Models\User;

class DoctorSchedulePolicy
{
    protected function canManage(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'super-admin', 'admin']) || $user->can('schedules.manage');
    }

    public function create(User $user): bool
    {
        return $this->canManage($user);
    }

    public function delete(User $user, DoctorSchedule $schedule): bool
    {
        return $this->canManage($user);
    }
}
