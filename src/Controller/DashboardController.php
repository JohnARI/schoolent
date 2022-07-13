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
use App\Repository\GradeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;

class DashboardController extends AbstractController
{

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, CacheInterface $cache)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->cache = $cache;
    }

    /**
     * Afficher les utilsateurs : le nombre d'administrateurs, d'élèves, et professeurs et leurs calendriers respectifs(élèves et professeurs).
     * @Route("admin/dashboard", name="dashboard-admin")
     */
    public function admin(Request $request): Response
    {
        $grade = new Grade();
        
        $formGrade = $this->createForm(GradeType::class, $grade);
        $formGrade->handleRequest($request);
        $session = $this->entityManager->getRepository(Session::class)->findAll();
        $myStudents = $this->entityManager->getRepository(User::class)->findBySession('ROLE_USER', $this->getUser()->getSession());
        $mySession = $this->getUser()->getSession($session);
        $gradeTeacher = $this->entityManager->getRepository(Grade::class)->findGradeByTeacher($this->getUser()->getId(), $mySession);
        $results = $this->cache->get('dashboard-admin', function(ItemInterface $item) use ($myStudents) {
            $item->expiresAfter(3600);
            return [ 
            'users' => $this->entityManager->getRepository(User::class)->findAll(),
            'students' => $this->entityManager->getRepository(User::class)->findByRole('ROLE_USER'),
            'teachers' => $this->entityManager->getRepository(User::class)->findByRole('ROLE_TEACHER'),
            'admins' => $this->entityManager->getRepository(User::class)->findByRole('ROLE_ADMIN'),
            'sessions' => $this->entityManager->getRepository(Session::class)->findAll(),
            'studentsWoman' => $this->entityManager->getRepository(User::class)->findBySexeStudent(1),
            'studentsMan' => $this->entityManager->getRepository(User::class)->findBySexeStudent(0),
            'gradeStudents' => $this->entityManager->getRepository(Grade::class)->findByUser($myStudents),
            'dateByMonth' => $this->entityManager->getRepository(Calendar::class)->findDateMonth(),
            ];
        });

        if ($formGrade->isSubmitted() && $formGrade->isValid()) {
            $grade->setCreatedAt(new DateTimeImmutable());
            $grade->setTeacher($this->getUser());
            $grade->setSession($mySession);
            $this->entityManager->persist($grade);
            $this->entityManager->flush();
            $this->addFlash('success', 'La note a été attribué');
            return $this->redirect($request->getUri());   
        }
        

        return $this->render('dashboard/admins-dashboard.html.twig', [
            'gradeTeacher' => $gradeTeacher,
            'results' => $results,
            'myStudents' => $myStudents,
            'formGrade' => $formGrade->createView(),
        ]);
    }

    /**
     * Afficher la liste des élèves par session, son emploi du temps, le nombre d'intervention et sa rémunération.
     * @Route("teacher/dashboard", name="dashboard-teacher")
     */
    public function teacher(Request $request): Response
    {
        $grade = new Grade();
        $formGrade = $this->createForm(GradeType::class, $grade);
        $formGrade->handleRequest($request);
        $session = $this->entityManager->getRepository(Session::class)->findAll();
        $myStudents = $this->entityManager->getRepository(User::class)->findBySession('ROLE_USER', $this->getUser()->getSession());
        $mySession = $this->getUser()->getSession($session);
        $gradeTeacher = $this->entityManager->getRepository(Grade::class)->findGradeByTeacher($this->getUser()->getId(), $mySession);
       
        
        $results = $this->cache->get('dashboard', function(ItemInterface $item) use($myStudents) {
            $item->expiresAfter(3600);
            return [ 
                'gradeStudents' => $this->entityManager->getRepository(Grade::class)->findByUser($myStudents),
            ];
        });

        

        if ($formGrade->isSubmitted() && $formGrade->isValid()) {
            $grade->setCreatedAt(new DateTimeImmutable());
            $grade->setTeacher($this->getUser());
            $grade->setSession($mySession);
            $this->entityManager->persist($grade);
            $this->entityManager->flush();
            $this->addFlash('success', 'La note a été attribué');
            return $this->redirect($request->getUri());
            
        }

        return $this->render('dashboard/teachers-dashboard.html.twig', [
            'gradeTeacher' => $gradeTeacher,
            'results' => $results,
            'myStudents' => $myStudents,
            'formGrade' => $formGrade->createView(),
        ]);
    }

    /**
     * Afficher la session, l'emploi du temps et le nom du professeur. 
     * @Route("/dashboard", name="dashboard-student")
     */
    public function student(UserRepository $userRepository, GradeRepository $gradeRepository): Response
    {

        $teachers = $userRepository->findBySession('ROLE_TEACHER', $this->getUser()->getSession());
        $students = $userRepository->findBySession('ROLE_USER', $this->getUser()->getSession());
        if ($this->getUser()->getRole() == 'Eleve') {
            $studentId = $this->getUser()->getId();
            $grades = $gradeRepository->findGradeByUser($studentId);
        }

        return $this->render('dashboard/students-dashboard.html.twig', [
            'teachers' => $teachers,
            'students' => $students,
            'grades' => $grades,
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
