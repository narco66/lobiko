<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PurgeOldSessionsCommand extends Command
{
    protected $signature = 'security:purge-sessions {days=30 : Supprimer les sessions plus anciennes que N jours}';

    protected $description = 'Purge les sessions expirées pour réduire la surface d’attaque.';

    public function handle(): int
    {
        $days = (int) $this->argument('days');
        $cutoff = now()->subDays($days);

        $deleted = DB::table('sessions')
            ->where('last_activity', '<', $cutoff->timestamp)
            ->delete();

        $this->info("Sessions purgées : {$deleted}");
        return self::SUCCESS;
    }
}
