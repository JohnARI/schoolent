<?php

namespace App\Notification;

use App\Entity\User;
use App\Entity\Notification;
use Tightenco\Collect\Support\Collection;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractNotification
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {

        $this->entityManager = $entityManager;
    }

    /**
     * Génère l'envoi d'une notification simple à l'utilisateurs.
     * @param Notification $notification
     * @param User $user
     * @return bool
     */
    protected function sendNotifSimple(Notification $notification, User $user): bool
    {
        # Récupération des utilisateurs à notifier via le role
        $notification->setUser($user);
        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        return true;
    }
}