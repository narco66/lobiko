<?php

namespace App\Notifications;

use App\Models\Paiement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceived extends Notification
{
    use Queueable;

    public function __construct(public Paiement $paiement)
    {
    }

    /**
     * Create a new notification instance.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $facture = $this->paiement->facture;

        $mail = (new MailMessage)
            ->subject('Paiement reçu')
            ->greeting('Bonjour,')
            ->line("Votre paiement {$this->paiement->numero_paiement} a été confirmé.")
            ->line("Montant : {$this->paiement->montant} {$this->paiement->devise}");

        if ($facture) {
            $mail->line("Facture : {$facture->numero_facture}");
        }

        return $mail
            ->line('Merci pour votre confiance.')
            ->action('Voir mes paiements', url('/dashboard'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
