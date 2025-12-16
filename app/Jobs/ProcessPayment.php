<?php

namespace App\Jobs;

use App\Models\Paiement;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessPayment implements ShouldQueue
{
    use Queueable;

    public function __construct(public Paiement $paiement)
    {
    }

    /**
     * Create a new job instance.
     */
    public function handle(): void
    {
        if ($this->paiement->statut !== 'en_cours' && $this->paiement->statut !== 'initie') {
            return;
        }

        $this->paiement->update([
            'statut' => 'en_cours',
            'derniere_tentative' => now(),
            'tentatives' => ($this->paiement->tentatives ?? 0) + 1,
        ]);

        // Ici se branchera la passerelle de paiement (mocké pour l’instant)
        $this->paiement->markAsConfirmed();
    }
}
