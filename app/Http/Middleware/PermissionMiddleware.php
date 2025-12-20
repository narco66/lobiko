<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        $permissions = $this->normalize($permissions);

        foreach ($permissions as $permission) {
            if ($user->can($permission) || (method_exists($user, 'hasRole') && $user->hasRole($permission))) {
                return $next($request);
            }
        }

        abort(403);
    }

    private function normalize(array $items): array
    {
        if (count($items) === 1 && str_contains($items[0], '|')) {
            return explode('|', $items[0]);
        }
        return $items;
    }
}
