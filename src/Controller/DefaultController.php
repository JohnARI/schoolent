<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DefaultController extends AbstractController
{

    /**
     * Dashboard Redirect.
     * @Route("/")
     */
    public function index(): Response
    {
        if ($this->isGranted('ROLE_USER') == false) {
            return $this->redirectToRoute('login');
        } else {
            return $this->redirectToRoute('dashboard');
        }

      
    

    }
}

