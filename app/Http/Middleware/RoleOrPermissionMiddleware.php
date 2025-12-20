<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleOrPermissionMiddleware
{
    public function handle(Request $request, Closure $next, ...$params)
    {
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        $params = $this->normalize($params);

        if (method_exists($user, 'hasAnyRole') && $user->hasAnyRole($params)) {
            return $next($request);
        }

        foreach ($params as $param) {
            if ($user->can($param) || (method_exists($user, 'hasRole') && $user->hasRole($param))) {
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
