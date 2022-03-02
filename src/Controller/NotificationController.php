<?php

namespace App\Controller;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * Supprimer toute les notifications
     * @Route("/dashboard/notification/delete_notifications_all", name="notification_delete_all",methods={"GET"})
     */
    public function deleteNotificationAll(Request $request): Response
    {
        foreach ($this->getUser()->getNotifications() as $notification) {
            $this->entityManager->remove($notification);
            $this->entityManager->flush();
        }

        return $this->redirect($request->get('redirect') ?? '/');
    }

    /**
     * Supprimer une notification
     * @Route("/dashboard/notification/{id}/delete", name="notification_delete",methods={"GET"})
     */
    public function clearNotification(Notification $notification, Request $request): Response
    {
        $this->entityManager->remove($notification);
        $this->entityManager->flush();

        return $this->redirect($request->get('redirect') ?? '/');
    }
}
