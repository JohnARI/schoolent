<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Course;
use DateTimeImmutable;
use App\Entity\Session;
use App\Entity\Calendar;
use App\Form\CourseType;
use App\Service\Mailjet;
use App\Form\SessionType;
use App\Form\EditUserType;
use App\Form\RegisterType;
use App\Form\EditSessionType;
use App\Form\EditCalendarType;
use App\Repository\UserRepository;
use App\Service\PasswordGenerator;
use App\Entity\ProgrammingLanguage;
use App\Form\AddProgrammingLanguageType;
use App\Form\CalendarAdminType;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\EditProgrammingLanguageType;
use App\Notification\NotificationService;
use App\Service\FileUploader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdministrationController extends AbstractController
{

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, Mailjet $mailjet, FileUploader $fileUploader, NotificationService $notification)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->mailjet = $mailjet;
        $this->fileUploader = $fileUploader;
        $this->notification = $notification;
    }


    /**
     * @Route("/admin/view-users", name="view-users")
     */
    public function viewUsers(): Response
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();

        return $this->render('administration/admin/view_users.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/admin/view-all", name="view-all")
     */
    public function viewAll(Request $request, SluggerInterface $slugger, PasswordGenerator $passwordGenerator, string $projectDir, UserRepository $userRepository): Response
    {
        // Tableaux
        $users = $this->entityManager->getRepository(User::class)->findAll();
        $programmingLanguages = $this->entityManager->getRepository(ProgrammingLanguage::class)->findAll();
        $sessions = $this->entityManager->getRepository(Session::class)->findAll();
        $calendars = $this->entityManager->getRepository(Calendar::class)->findAll();
        $courses = $this->entityManager->getRepository(Course::class)->findAll();
        $students = $userRepository->findBySession('ROLE_USER', $this->getUser()->getSession());

        // Fin tableaux

        // Add user
        $temporaryPassword = $passwordGenerator->passwordAleatoire(20);
        $user = new User();
        $formUser = $this->createForm(RegisterType::class, $user);
        $formUser->handleRequest($request);

        if ($formUser->isSubmitted() && $formUser->isValid()) {

            // Ajout de photo
            $file = $formUser->get('picture')->getData();

            if ($file) {
                $newFilename = $this->fileUploader->upload($file, '/user');
                $user->setPicture($newFilename);
            }
            $user->setPassword($this->passwordHasher->hashPassword($user, $temporaryPassword));
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->mailjet->sendEmail($user, 'Bienvenue Chez SCHOOLENT! Voici votre mot de passe temporaire :'   . $temporaryPassword);
            $this->addFlash('success', 'Votre ajout a bien été pris en compte, un mail a été envoyé!');
            //Message de succès
            return $this->redirect($request->getUri());
        }
        // Fin add user
        // Add techno

        $techno = new ProgrammingLanguage();

        $formTechno = $this->createForm(AddProgrammingLanguageType::class, $techno);
        $formTechno->handleRequest($request);

        if ($formTechno->isSubmitted() && $formTechno->isValid()) {
            $technoPicture = $formTechno->get('picture')->getData();

            if ($technoPicture) {
                $newFilename = $this->fileUploader->upload($technoPicture, '/techno');
                $techno->setPicture($newFilename);
            }

            $this->entityManager->persist($techno);
            $this->entityManager->flush();
            $this->addFlash('success', 'Un nouveau programme a été ajouté !');
            return $this->redirect($request->getUri());
        }
        // Fin add techno

        // Add session

        $session = new Session();

        $formSession = $this->createForm(SessionType::class, $session);
        $formSession->handleRequest($request);

        if ($formSession->isSubmitted() && $formSession->isValid()) {

            $session->setCreatedAt(new DateTimeImmutable());

            $this->entityManager->persist($session);
            $this->entityManager->flush();
            return $this->redirect($request->getUri());
            $this->addFlash('success', 'Une nouvelle session a été ajoutée !');
        }
        // Fin add session

        // Add Calendar

        $calendar = new Calendar();
        $student = new User();

        $formCalendar = $this->createForm(CalendarAdminType::class, $calendar);

        $formCalendar->handleRequest($request);

        if ($formCalendar->isSubmitted() && $formCalendar->isValid()) {

            $session = $formCalendar->get('session')->getData();
            $students = $this->entityManager->getRepository(User::class)->findBySession('ROLE_USER', $session);

            $teacher = $formCalendar->get('teacher')->getData();
            $cours = $formCalendar->get('name')->getData();
            $programmingLanguages = $formCalendar->get('category')->getData()->getName();
            $dateStart = $formCalendar->get('start')->getData();
            $dateEnd = $formCalendar->get('end')->getData();
            $nameSession = $formCalendar->get('session')->getData()->getName();


            $calendar->setCreatedAt(new DateTime());

            $this->entityManager->persist($calendar);
            $this->entityManager->flush();

            $this->notification->sendNotification("Vous avez un nouveau cours de: " . $programmingLanguages . "du" . date_format($dateStart, 'd-m-y') . " Au " . date_format($dateEnd, 'd-m-y.'), $teacher);

            $this->mailjet->sendEmail($teacher, "Votre planning pour la semaine du " . date_format($dateStart, 'd-m-y') . " Au " . date_format($dateEnd, 'd-m-y.') . "intervention sur " . $cours . " " . $programmingLanguages . " Nom de session " . $nameSession . ".");
            if ($student) {
                foreach ($students as $student) {
                    $this->mailjet->sendEmail($student, "Voici votre convocation pour le cours " . $cours . " " . $programmingLanguages . " de la semaine du  : " . date_format($dateStart, 'd-m-y') . " Au " . date_format($dateEnd, 'd-m-y.') . " Avec le professeur " . $teacher . '.');
                }
            }

            $this->addFlash('success', 'Une nouvelle a été date ajoutée !');
            return $this->redirect($request->getUri());
        }
        // Fin add calendar


        // Add cours
        $course = new Course();

        $formCourse = $this->createForm(CourseType::class, $course);
        $formCourse->handleRequest($request);

        if ($formCourse->isSubmitted() && $formCourse->isValid()) {
            $courseFile = $formCourse->get('link')->getData();

            if ($courseFile) {
                $newFilename = $this->fileUploader->upload($courseFile, '/cours');
                $course->setLink($newFilename);
            }

            $this->entityManager->persist($course);
            $this->entityManager->flush();
            $this->addFlash('success', 'Un nouveau cours ajouté !');
            return $this->redirect($request->getUri());
        }

        return $this->render('administration/admin/view_all.html.twig', [
            'users' => $users,
            'programmingLanguages' => $programmingLanguages,
            'sessions' => $sessions,
            'calendars' => $calendars,
            'formUser' => $formUser->createView(),
            'formCalendar' => $formCalendar->createView(),
            'formSession' => $formSession->createView(),
            'formCourse' => $formCourse->createView(),
            'formTechno' => $formTechno->createView(),
            'courses' => $courses,
            'students' => $students,
        ]);
    }

    /**
     * @Route("/admin/edit/user/{id}", name="edit_user")
     */
    public function editUser($id, Request $request, SluggerInterface $slugger, string $projectDir): Response
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);

        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);
        $fileName = $user->getPicture();
        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('picture')->getData();

            if ($file) {
                $newFilename = $this->fileUploader->upload($file, '/user');
                $user->setPicture($newFilename);
            } elseif (is_null($file)) {
                $filesystem = new Filesystem();
                $filesystem->remove($projectDir . '/public/uploads/user/' . $fileName);
            }

            $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash('success', 'L\'utilisateur a été modifié !');
            return $this->redirect($request->get('redirect') ?? '/admin/view-all');
        }

        return $this->render('administration/admin/edit/edit_user.html.twig', [

            'form' => $form->createView()
        ]);
    }

     /**
     * @Route("/profil/{id}", name="profil_user")
     */
    public function profilUser($id): Response
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);

        

        return $this->render('profil/profil_user.html.twig', [
            'user' => $user,
        
        ]);
    }

    /**
     * @Route("/admin/delete/user/{id}", name="delete_user")
     */
    public function deleteUser(User $user, Request $request, string $projectDir): Response
    {
        $fileName = $user->getPicture();

        // suppression de la photo user
        if ($fileName) {
            $filesystem = new Filesystem();
            $imageDir = $this->getParameter('kernel.project_dir');
            $filesystem->remove($imageDir . '/public/uploads/user/' . $fileName);
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();
        $this->addFlash('success', 'L\'utilisateur a été suprimmé !');
        return $this->redirect($request->get('redirect') ?? '/admin/view-all');
    }

    /**
     * @Route("/admin/edit/technologie/{id}", name="edit_technologie", methods={"GET|POST"})
     */
    public function editTechnologie($id, Request $request, SluggerInterface $slugger): Response
    {

        $technologie = $this->entityManager->getRepository(ProgrammingLanguage::class)->find($id);

        $form = $this->createForm(EditProgrammingLanguageType::class, $technologie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $technoPicture = $form->get('picture')->getData();

            if ($technoPicture) {
                $newFilename = $this->fileUploader->upload($technoPicture, '/techno');
                $technologie->setPicture($newFilename);
            }

            $this->entityManager->persist($technologie);
            $this->entityManager->flush();
            $this->addFlash('success', 'Le programme a été modifié !');
            return $this->redirect($request->get('redirect') ?? '/admin/view-all');
        }

        return $this->render('administration/admin/edit/edit_technologies.html.twig', [

            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/delete/technologie/{id}", name="delete_technologie")
     */
    public function deleteTehnologie(ProgrammingLanguage $technologie, Request $request): Response
    {
        $fileName = $technologie->getPicture();

        // suppression de la photo technologie
        if ($fileName) {
            $filesystem = new Filesystem();
            $projectDir = $this->getParameter('kernel.project_dir');
            $filesystem->remove($projectDir . '/public/uploads/techno/' . $fileName);
        }

        $this->entityManager->remove($technologie);
        $this->entityManager->flush();

        $this->addFlash('success', 'Le programme a été suprimmé !');
        return $this->redirect($request->get('redirect') ?? '/admin/view-all');
    }

    /**
     * @Route("/admin/edit/calendar/{id}", name="edit_calendar",methods={"GET|POST"})
     */
    public function editCalendar($id, Request $request, UserRepository $userRepository): Response
    {
        $students = new User();
        $calendar = $this->entityManager->getRepository(Calendar::class)->findBy(['id' => $id]);

        $formCalendar = $this->createForm(EditCalendarType::class, $calendar[0]);
        $formCalendar->handleRequest($request);

        // dd($calendar);

        if ($formCalendar->isSubmitted() && $formCalendar->isValid()) {

            $session = $formCalendar->get('session')->getData();
            $students = $this->entityManager->getRepository(User::class)->findBySession('ROLE_USER', $session);

            $teacher = $formCalendar->get('teacher')->getData();
            $cours = $formCalendar->get('name')->getData();
            $programmingLanguages = $formCalendar->get('category')->getData()->getName();
            $dateStart = $formCalendar->get('start')->getData();
            $dateEnd = $formCalendar->get('end')->getData();
            $nameSession = $formCalendar->get('session')->getData()->getName();

            // dd($student);

            $this->entityManager->persist($calendar[0]);
            $this->entityManager->flush();

            $this->notification->sendNotification("Votre intervention a été modifiée : " . date_format($dateStart, 'd-m-y') . " Au " . date_format($dateEnd, 'd-m-y.'), $teacher);
            $this->mailjet->sendEmail($teacher, "Votre planning vient d'être mis à jour. Nouvelle intervention sur " . $cours . $programmingLanguages . " du : " . date_format($dateStart, 'd-m-y') . " Au " . date_format($dateEnd, 'd-m-y.') . " Nom de session " . $nameSession . ".");

            foreach ($students as $student) {
                $this->mailjet->sendEmail($student, "votre convocation vient d'être mis à jour pour le cours " . $cours . $programmingLanguages . " du : " . date_format($dateStart, 'd-m-y') . " Au " . date_format($dateEnd, 'd-m-y.') . " Avec le professeur " . $teacher . '.');
            }

            $this->addFlash('success', 'La date a été modifiée !');
            return $this->redirect($request->get('redirect') ?? '/admin/view-all');
        }

        return $this->render('administration/admin/edit/edit_calendar.html.twig', [
            'form' => $formCalendar->createView(),
        ]);
    }

    /**
     * @Route("/admin/delete/calendar/{id}", name="delete_calendar")
     */
    public function deleteCalendar(Calendar $calendar, Request $request): Response
    {
        $this->entityManager->remove($calendar);
        $this->entityManager->flush();

        return $this->redirect($request->get('redirect') ?? '/admin/view-all');
        $this->addFlash('success', 'La date a été supprimée');
    }

    /**
     * @Route("/admin/edit/session/{id}", name="edit-session",methods={"GET|POST"})
     */
    public function editSession(Session $session, Request $request): Response
    {
        $form = $this->createForm(EditSessionType::class, $session);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($session);
            $this->entityManager->flush();
            return $this->redirect($request->get('redirect') ?? '/admin/view-all');
            $this->addFlash('success', 'La session a été modifiée !');
        }

        return $this->render('administration/admin/edit/edit-session.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/delete/session/{id}", name="delete-session", methods={"GET"})
     */
    public function deleteSession(Session $session, Request $request): Response
    {
        $this->entityManager->remove($session);
        $this->entityManager->flush();
        return $this->redirect($request->get('redirect') ?? '/admin/view-all');
        $this->addFlash('success', 'La session a été supprimée');
    }

    /**
     * @Route("/admin/delete/cours/{id}", name="delete-cours",methods={"GET"})
     */
    public function clearCourse(Course $course, Request $request): Response
    {
        $fileName = $course->getLink();
        // suppression de la photo technologie
        if ($fileName) {
            $filesystem = new Filesystem();
            $projectDir = $this->getParameter('kernel.project_dir');
            $filesystem->remove($projectDir . '/public/uploads/cours/' . $fileName);
        }

        $this->entityManager->remove($course);
        $this->entityManager->flush();

        return $this->redirect($request->get('redirect') ?? '/admin/view-all');
        $this->addFlash('success', 'Le cours a été supprimé');
    }
}
