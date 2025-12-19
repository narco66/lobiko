<?php

namespace App\Policies;

use App\Models\BlogTag;
use App\Models\User;

class BlogTagPolicy
{
    protected function canManage(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'super-admin', 'admin']) || $user->can('blog.view');
    }

    public function viewAny(User $user): bool
    {
        return $this->canManage($user);
    }

    public function view(User $user, BlogTag $tag): bool
    {
        return $this->canManage($user);
    }

    public function create(User $user): bool
    {
        return $user->can('blog.create') || $this->canManage($user);
    }

    public function update(User $user, BlogTag $tag): bool
    {
        return $user->can('blog.update') || $this->canManage($user);
    }

    public function delete(User $user, BlogTag $tag): bool
    {
        return $user->can('blog.delete') || $this->canManage($user);
    }
}
