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
        }
         elseif($this->getUser()->getRole() == 'Administrateur') {
            return $this->redirectToRoute('dashboard-admin');
        }
         elseif($this->getUser()->getRole() == 'Formateur') {
            return $this->redirectToRoute('dashboard-teacher');
        }
         elseif($this->getUser()->getRole() == 'Eleve') {
            return $this->redirectToRoute('dashboard-student');
        }

      
    

    }
}

