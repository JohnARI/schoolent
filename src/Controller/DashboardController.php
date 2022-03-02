<?php

namespace App\Controller;

use App\Entity\Calendar;
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
     * @Route("admin/dashboard", name="dashboard")
     */
    public function index(): Response
    {
        
        $users = $this->entityManager->getRepository(User::class)->findAll();
        $students = $this->entityManager->getRepository(User::class)->findByRole('ROLE_USER');
        $teachers = $this->entityManager->getRepository(User::class)->findByRole('ROLE_TEACHER');
        $admins = $this->entityManager->getRepository(User::class)->findByRole('ROLE_ADMIN');
        $sessions = $this->entityManager->getRepository(Session::class)->findAll();
        $studentsWoman = $this->entityManager->getRepository(User::class)->findBySexeStudent(1);
        $studentsMan = $this->entityManager->getRepository(User::class)->findBySexeStudent(0);
        $dateByMonth = $this->entityManager->getRepository(Calendar::class)->findDateMonth();

        // $calendarByMonth = json_encode($calendarByMonth);
  
        // dd($students);
        // dd($sexe);
        // dd($studentsMan);
        // dd($calendarByMonth);
  
        return $this->render('dashboard/admins-dashboard.html.twig', [
            'users' => $users,
            'students' => $students,
            'teachers' => $teachers,
            'admins' => $admins,
            'sessions' => $sessions,
            'studentsWoman' => $studentsWoman,
            'studentsMan' => $studentsMan,
            'dateByMonth' => $dateByMonth
        ]);
    }

    /**
     * @Route("admin/teachers", name="view-teachers")
     */
    public function showTeacher($role = 'ROLE_TEACHER'): Response
    {

        $users = $this->entityManager->getRepository(User::class)->findByRole($role);

        return $this->render("administration/admin/view/view_teachers.html.twig",[
            'users' => $users,
        ]);
    }

    /**
     * @Route("admin/admins", name="view-admins")
     */
    public function showAdmin($role = 'ROLE_ADMIN'): Response
    {

        $admins = $this->entityManager->getRepository(User::class)->findByRole($role);


        return $this->render("administration/admin/view/view_admins.html.twig", [
            'admins' => $admins,
        ]);
    }

    /**
     * @Route("teacher/students", name="view-students")
     */
    public function showUser(UserRepository $userRepository): Response
    {

        $users = $userRepository->findBySession('ROLE_USER', $this->getUser()->getSession());
        

        

        return $this->render("administration/admin/view/view_students.html.twig", [
            'users' => $users,
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
