<?php

namespace App\Notification;

use App\Entity\Notification;
use App\Entity\User;


class NotificationService extends AbstractNotification
{
    /**
     * @return bool
     * # TODO Remplacer l'URL en dur via .env
     */
    public function sendNotification(string $message, User $user, string $link = ''): bool
    {
        # Création d'une notification
        $notification = Notification::create($message, $link);

        # Envoi de la notification
        return $this->sendNotifSimple($notification, $user);
    }

    
}