<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PurgeOldActivityLogsCommand extends Command
{
    protected $signature = 'security:purge-activity {days=180 : Supprimer les logs plus anciens que N jours}';

    protected $description = 'Purge les anciens activity_log pour conformité et réduction du volume.';

    public function handle(): int
    {
        $days = (int) $this->argument('days');
        $cutoff = now()->subDays($days);

        $deleted = DB::table('activity_log')
            ->where('created_at', '<', $cutoff)
            ->delete();

        $this->info("Logs supprimés : {$deleted}");
        return self::SUCCESS;
    }
}
