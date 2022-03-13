<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Grade;
use DateTimeImmutable;
use App\Entity\Contact;
use App\Entity\Session;
use App\Form\GradeType;
use App\Entity\Calendar;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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
     * Afficher les utilsateurs : le nombre d'administrateurs, d'élèves, et professeurs et leurs calendriers respectifs(élèves et professeurs).
     * @Route("admin/dashboard", name="dashboard-admin")
     */
    public function admin(UserRepository $userRepository, Request $request): Response
    {      
        $users = $this->entityManager->getRepository(User::class)->findAll();
        $students = $this->entityManager->getRepository(User::class)->findByRole('ROLE_USER');
        $teachers = $this->entityManager->getRepository(User::class)->findByRole('ROLE_TEACHER');
        $admins = $this->entityManager->getRepository(User::class)->findByRole('ROLE_ADMIN');
        $sessions = $this->entityManager->getRepository(Session::class)->findAll();
        $studentsWoman = $this->entityManager->getRepository(User::class)->findBySexeStudent(1);
        $studentsMan = $this->entityManager->getRepository(User::class)->findBySexeStudent(0);
        $dateByMonth = $this->entityManager->getRepository(Calendar::class)->findDateMonth();
        $session = $this->entityManager->getRepository(Session::class)->findAll();
        $mySession = $this->getUser()->getSession($session);
        $myStudents = $userRepository->findBySession('ROLE_USER', $this->getUser()->getSession());

        $grade = new Grade();

        $formGrade = $this->createForm(GradeType::class, $grade);
        $formGrade->handleRequest($request);
        
        if ($formGrade->isSubmitted() && $formGrade->isValid()) {
            $grade->setCreatedAt(new DateTimeImmutable());
            $this->entityManager->persist($grade);
            $this->entityManager->flush();
            $this->addFlash('success', 'La note a été attribué');
            return $this->redirect($request->getUri());
        }

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
            'dateByMonth' => $dateByMonth,
            'mySessions' => $mySession,
            'myStudents' => $myStudents,
            'formGrade' => $formGrade->createView(),
        ]);
    }

    /**
     * Afficher la liste des élèves par session, son emploi du temps, le nombre d'intervention et sa rémunération.
     * @Route("teacher/dashboard", name="dashboard-teacher")
     */
    public function teacher(UserRepository $userRepository, Request $request): Response
    {
        $session = $this->entityManager->getRepository(Session::class)->findAll();
        $myStudents = $userRepository->findBySession('ROLE_USER', $this->getUser()->getSession());
        $mySession = $this->getUser()->getSession($session);

        $grade = new Grade();
        
        $formGrade = $this->createForm(GradeType::class, $grade);
        $formGrade->handleRequest($request);

        if ($formGrade->isSubmitted() && $formGrade->isValid()) {

            $this->entityManager->persist($grade);
            $this->entityManager->flush();
            $this->addFlash('success', 'L\'utilisateur a été modifié !');
            return $this->redirect($request->getUri());
        }





        return $this->render('dashboard/teachers-dashboard.html.twig', [
            'myStudents' => $myStudents,
            'mySessions' => $mySession,
            'formGrade' => $formGrade->createView(),
        ]);
    }

    /**
     * Afficher la session, l'emploi du temps et le nom du professeur. 
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
