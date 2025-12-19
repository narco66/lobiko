<?php

namespace App\Policies;

use App\Models\MediaFile;
use App\Models\User;

class MediaFilePolicy
{
    protected function canManage(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'super-admin', 'admin']) || $user->can('blog.media.manage');
    }

    public function viewAny(User $user): bool
    {
        return $this->canManage($user);
    }

    public function view(User $user, MediaFile $media): bool
    {
        return $this->canManage($user);
    }

    public function delete(User $user, MediaFile $media): bool
    {
        return $this->canManage($user);
    }
}
