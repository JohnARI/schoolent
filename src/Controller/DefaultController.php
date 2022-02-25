<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DefaultController extends AbstractController
{

    /**
     * Dashboard Redirect.
     * @Route("/", name="default")
     */
    public function index(): Response

    {

        if ($this->isGranted('ROLE_USER') == false) {
            return $this->redirectToRoute('login');
        } elseif ($user = $this->getUser()) {
            $role = $user->getRoles();

            switch ($role[0]) {

                case 'ROLE_ADMIN':

                    return $this->redirectToRoute('dashboard-admin');
                    break;

                case 'ROLE_TEACHER':

                    return $this->redirectToRoute('dashboard-teacher');
                    break;


                case 'ROLE_USER':

                    return $this->redirectToRoute('dashboard-student');
                    break;
            }
        }
    }
}
