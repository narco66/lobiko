<?php

namespace App\Providers;

use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class ConsoleSafetyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Event::listen(CommandStarting::class, function (CommandStarting $event) {
            $command = $event->command;

            if (!$command) {
                return;
            }

            $dangerous = [
                'migrate:fresh',
                'migrate:refresh',
                'db:wipe',
                'schema:drop',
            ];

            $env = App::environment();

            // Log all artisan commands to help trace destructive actions
            Log::channel('daily')->info('[artisan]', [
                'command' => $command,
                'env' => $env,
                'argv' => $event->input?->getArguments(),
                'options' => $event->input?->getOptions(),
            ]);

            // Block destructive commands outside local/testing unless explicitly allowed
            $allowed = in_array($env, ['local', 'development', 'testing']);
            $forceAllow = (bool) env('ALLOW_DESTRUCTIVE_COMMANDS', false);

            if (in_array($command, $dangerous, true) && !$allowed && !$forceAllow) {
                $message = "Commande '{$command}' bloquée en environnement {$env}. Définir ALLOW_DESTRUCTIVE_COMMANDS=true pour forcer (à éviter).";
                Log::warning($message);
                throw new \RuntimeException($message);
            }
        });
    }
}
