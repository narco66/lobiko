<?php

namespace App\Console\Commands;

use App\Jobs\ProcessPayment;
use App\Models\Paiement;
use Illuminate\Console\Command;

class ProcessPendingPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-pending-payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Traiter les paiements en attente (initie/en_cours)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pending = Paiement::pending()->get();

        if ($pending->isEmpty()) {
            $this->info('Aucun paiement en attente.');
            return self::SUCCESS;
        }

        $this->info("Traitement de {$pending->count()} paiement(s)...");

        $pending->each(function (Paiement $paiement) {
            ProcessPayment::dispatch($paiement);
        });

        $this->info('Jobs dispatchés.');

        return self::SUCCESS;
    }
}
