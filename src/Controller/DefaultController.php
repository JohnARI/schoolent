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
        $roles = $this->getUser()->getRole();

        switch ($roles) {
            case $this->isGranted('ROLE_USER') == false: 
        return $this->redirectToRoute('login');
        break;
        default: return $this->redirectToRoute('dashboard');
        break;
    

    }
}

}