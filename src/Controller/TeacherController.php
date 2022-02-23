<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Session;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TeacherController extends AbstractController
{


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        
    }
    /**
     * @Route("/teacher", name="teacher")
     */
    public function index(): Response
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();
        $students = $this->entityManager->getRepository(User::class)->findByRole('ROLE_USER');
        $sessions = $this->entityManager->getRepository(Session::class)->findAll();
        $studentsWoman = $this->entityManager->getRepository(User::class)->findBySexeUser('ROLE_USER', 1);
        $studentsMan = $this->entityManager->getRepository(User::class)->findBySexeUser('ROLE_USER', 0);
        // dd($students);
        // dd($sexe);
        
        $this->addFlash('message_error', 'Vous ne pouvez selectionner une date de fin antérieure à la date de début');

        return $this->render('dashboard/teachers-dashboard.html.twig', [
            'users' => $users,
            'students' => $students,
            'sessions' => $sessions,
            'studentsWoman' => $studentsWoman,
            'studentsMan' => $studentsMan,
        ]);
}

}
