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

    /**
     * Génère l'envoi d'une notification aux utilisateurs d'un même ROLE.
     * @param Notification $notification
     * @param string $role
     * @return bool
     */
    protected function sendByRole(Notification $notification, string $role = 'ROLE_ADMIN'): bool
    {
        # Récupération des utilisateurs à notifier via le role
        $users = $this->entityManager->getRepository(User::class)->findByRole($role);

        foreach ($users as $user) {

            $notification->setUser($user);
            $this->entityManager->persist($notification);
            $this->entityManager->flush();

            // $this->mailjet->sendNotificationEmail($user, $notification);
        }

        return true;
    }


    /**
     * Génère l'envoi d'une notification aux utilisateurs d'un même ROLE.
     * @param Notification $notification
     * @param array $roles
     * @param string $content
     * @return bool
     */
    protected function sendByRoles(Notification $notification, array $roles, string $content): bool
    {
        # Récupération des utilisateurs à notifier via le role
        $users = [];

        foreach ($roles as $role) {
            $users[] =  $this->entityManager->getRepository(User::class)->findByRole($role);
        }

        $users = new Collection($users);
        $users = $users->flatten();
        $users->all();


        foreach ($users as $user) {
            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                $notification->setUser($user);
                $this->entityManager->persist($notification);
            } else {
                $notification->setUser($user);
                $this->entityManager->persist($notification);
            }
            $this->entityManager->flush();
        }

        // $this->mailjet->sendPublicationRequestEmail($emails, $notification, $emailsAdmin, $content);

        return true;
    }
}