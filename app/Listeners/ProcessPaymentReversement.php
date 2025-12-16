<?php

namespace App\Listeners;

use App\Events\PaymentConfirmed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProcessPaymentReversement
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PaymentConfirmed $event): void
    {
        $facture = $event->paiement->facture;

        if (!$facture) {
            return;
        }

        // Le reversement sera déclenché quand la facture atteint l'état payé (via updateRemainingAmount)
        $facture->refresh()->updateRemainingAmount();
    }
}
