<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Envoie une notification à un utilisateur.
     *
     * @param int $userId L'ID de l'utilisateur à notifier.
     * @param string $title Le titre de la notification.
     * @param string $message Le corps du message de la notification.
     * @param string $type Le type de notification (e.g., 'ordonnance', 'rendezvous').
     * @param int|null $relatedId L'ID de l'entité liée (ordonnance, rendez-vous, etc.).
     * @return Notification|null
     */
    public function notifier(int $userId, string $title, string $message, string $type = 'general', ?int $relatedId = null)
    {
        try {
            $user = User::find($userId);

            if (!$user) {
                Log::warning("Tentative de notification à un utilisateur inexistant: ID {$userId}");
                return null;
            }

            $notification = Notification::create([
                'user_id' => $userId,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'related_id' => $relatedId,
                'read_at' => null, // Marquer comme non lu par défaut
            ]);

            // Ici, vous pouvez ajouter la logique pour envoyer la notification
            // par d'autres canaux (ex: email, push notification, WebSocket)
            // $user->notify(new CustomNotification($title, $message, $type, $relatedId));

            Log::info("Notification envoyée à l'utilisateur {$userId}: {$title}");

            return $notification;
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'envoi de la notification à l'utilisateur {$userId}: " . $e->getMessage());
            return null;
        }
    }
}
