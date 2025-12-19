<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Rate limiting for login and sensitive endpoints
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('sensitive', function (Request $request) {
            return Limit::perMinute(30)->by($request->ip());
        });

        // Cache warmup for actes médicaux (simple referentiel)
        Cache::remember('referentiels.actes', now()->addHours(6), function () {
            if (class_exists(\App\Models\ActeMedical::class) && Schema::hasTable('acte_medicaux')) {
                return \App\Models\ActeMedical::all();
            }
            return collect();
        });
    }
}
