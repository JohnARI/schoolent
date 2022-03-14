<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DefaultController extends AbstractController
{

    /**
     *
     * @Route("/", name="default")
     */
    public function index(): Response

    {

        if ($this->isGranted('ROLE_USER') == false) {
            return $this->redirectToRoute('login');
        }
        
        if ($user = $this->getUser()) {
            $role = $user->getRole();
            switch ($role) {
                

                case 'Eleve':

                    return $this->redirectToRoute('dashboard-student');
                    break;

                    case 'Formateur':                       

                        return $this->redirectToRoute('dashboard-teacher');
                        break;

                    case 'Administrateur':

                        return $this->redirectToRoute('dashboard-admin');
                        break;
            }
        }
    }
}
