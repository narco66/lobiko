<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        $roles = $this->normalize($roles);

        if (method_exists($user, 'hasAnyRole') && $user->hasAnyRole($roles)) {
            return $next($request);
        }

        abort(403);
    }

    private function normalize(array $roles): array
    {
        if (count($roles) === 1 && str_contains($roles[0], '|')) {
            return explode('|', $roles[0]);
        }
        return $roles;
    }
}
