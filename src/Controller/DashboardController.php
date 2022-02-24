<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Contact;
use App\Entity\Session;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DashboardController extends AbstractController
{

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Afficher les utilsateurs, le nombre des élèves, et formateurs, des administrateurs et de sétudiants.
     * @Route("admin/dashboard", name="dashboard-admin")
     */
    public function admin(): Response
    {

        $users = $this->entityManager->getRepository(User::class)->findAll();
        $students = $this->entityManager->getRepository(User::class)->findByRole('ROLE_USER');
        $teachers = $this->entityManager->getRepository(User::class)->findByRole('ROLE_TEACHER');
        $admins = $this->entityManager->getRepository(User::class)->findByRole('ROLE_ADMIN');
        $sessions = $this->entityManager->getRepository(Session::class)->findAll();
        $studentsWoman = $this->entityManager->getRepository(User::class)->findBySexeUser('ROLE_USER', 1);
        $studentsMan = $this->entityManager->getRepository(User::class)->findBySexeUser('ROLE_USER', 0);
        // dd($students);
        // dd($sexe);

        return $this->render('dashboard/admins-dashboard.html.twig', [
            'users' => $users,
            'students' => $students,
            'teachers' => $teachers,
            'admins' => $admins,
            'sessions' => $sessions,
            'studentsWoman' => $studentsWoman,
            'studentsMan' => $studentsMan,
        ]);
    }

    /**
     * Afficher les utilsateurs, le nombre des élèves, et formateurs, des administrateurs et de sétudiants.
     * @Route("teacher/dashboard", name="dashboard-teacher")
     */
    public function teacher(UserRepository $userRepository): Response
    {
        $session = $this->entityManager->getRepository(Session::class)->findAll();
        $students = $userRepository->findBySession('ROLE_USER', $this->getUser()->getSession());
        $sessions = $this->getUser()->getSession($session);
        return $this->render('dashboard/teachers-dashboard.html.twig', [
            'students' => $students,
            'sessions' => $sessions,
        ]);
    }

    /**
     * Afficher les utilsateurs, le nombre des élèves, et formateurs, des administrateurs et de sétudiants.
     * @Route("/dashboard", name="dashboard-student")
     */
    public function student(UserRepository $userRepository): Response
    {

        $teachers = $userRepository->findBySession('ROLE_TEACHER', $this->getUser()->getSession());
        $students = $userRepository->findBySession('ROLE_USER', $this->getUser()->getSession());

        return $this->render('dashboard/students-dashboard.html.twig', [
            'teachers' => $teachers,
            'students' => $students,
        ]);
    }


    /**
     * @Route("admin/teachers", name="view-teachers")
     */
    public function showTeacher(): Response
    {

        $teachers = $this->entityManager->getRepository(User::class)->findByRole('ROLE_TEACHER');

        return $this->render("administration/admin/view/view_teachers.html.twig", [
            'teachers' => $teachers,
        ]);
    }

    /**
     * @Route("admin/admins", name="view-admins")
     */
    public function showAdmin($role = 'ROLE_ADMIN'): Response
    {

        $users = $this->entityManager->getRepository(User::class)->findByRole($role);


        return $this->render("administration/admin/view/view_admins.html.twig", [
            'users' => $users,
        ]);
    }

    /**
     * @Route("teacher/students", name="view-students")
     */
    public function showUser(UserRepository $userRepository): Response
    {

        $students = $userRepository->findBySession('ROLE_USER', $this->getUser()->getSession());




        return $this->render("administration/admin/view/view_students.html.twig", [
            'students' => $students,
        ]);
    }

    /**
     * @Route("admin/contact", name="view-contact")
     */
    public function showContact(): Response
    {



        $contacts = $this->entityManager->getRepository(Contact::class)->findAll();

        return $this->render("administration/admin/view/view_contact.html.twig", [
            'contacts' => $contacts,
        ]);
    }
}
