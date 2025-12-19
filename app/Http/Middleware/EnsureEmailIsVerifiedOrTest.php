<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class EnsureEmailIsVerifiedOrTest
{
    /**
    * Bypass email verification for whitelisted test accounts when enabled.
    */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route('login'));
        }

        $allowBypass = Config::get('auth.allow_test_account_email_bypass', false);
        $whitelist = collect(Config::get('auth.test_accounts', []))
            ->map(fn ($email) => strtolower($email))
            ->filter()
            ->values();

        $isWhitelisted = $allowBypass && $whitelist->contains(strtolower($user->email));

        if ($user->hasVerifiedEmail() || $isWhitelisted) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            abort(403, 'Your email address is not verified.');
        }

        return redirect()->route('verification.notice');
    }
}
