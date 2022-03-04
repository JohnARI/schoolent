<?php

namespace App\Notification;

use App\Entity\Notification;
use App\Entity\User;


class NotificationService extends AbstractNotification
{
    /**
     * @param string $message
     * @param User $user
     * @param string $link
     * @return bool
     * # TODO Remplacer l'URL en dur via .env
     */
    public function sendNotification(string $message, User $user, string $link = 'https://secure.unlock.com'): bool
    {
        # Création d'une notification
        $notification = Notification::create($message, $link);

        # Envoi de la notification
        return $this->sendNotifSimple($notification, $user);
    }
    /**
     * @param string $message
     * @param string $link
     * @param string $role
     * @return bool
     * # TODO Remplacer l'URL en dur via .env
     */
    public function sendForRole(string $message, string $role = 'ROLE_ADMIN', string $link =  'https://secure.unlock.com'): bool
    {
        # Création d'une notification
        $notification = Notification::create($message, $link);

        # Envoi de la notification
        return $this->sendByRole($notification, $role);
    }

    /**
     * @param string $message
     * @param array $roles
     * @param string $content
     * @param string $link
     * @return bool
     */
    public function sendForRoles(string $message, string $content, array $roles = ['ROLE_ADMIN'], string $link =  'https://secure.unlock.com'): bool
    {
        # Création d'une notification
        $notification = Notification::create($message, $link);

        # Envoi de la notification
        return $this->sendByRoles($notification, $roles, $content);
    }

    
}