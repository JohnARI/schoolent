<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Course;
use DateTimeImmutable;
use App\Entity\Session;
use App\Entity\Calendar;
use App\Form\CourseType;
use App\Service\Mailjet;
use App\Form\SessionType;
use App\Form\CalendarType;
use App\Form\EditUserType;
use App\Form\RegisterType;
use App\Form\EditSessionType;
use App\Form\EditCalendarType;
use App\Service\PasswordGenerator;
use App\Entity\ProgrammingLanguage;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\EditProgrammingLanguageType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdministrationController extends AbstractController
{

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, Mailjet $mailjet)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->mailjet = $mailjet;
    }
    

    /**
     * @Route("/admin/view-users", name="view-users")
     */
    public function viewUsers(): Response
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();

        return $this->render('administration/admin/view_user.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/admin/view-all", name="view-all")
     */
    public function viewAll(Request $request, SluggerInterface $slugger, PasswordGenerator $passwordGenerator): Response
    {


        // Tableaux
        $users = $this->entityManager->getRepository(User::class)->findAll();
        $programmingLanguages = $this->entityManager->getRepository(ProgrammingLanguage::class)->findAll();
        $sessions = $this->entityManager->getRepository(Session::class)->findAll();
        $calendars= $this->entityManager->getRepository(Calendar::class)->findAll();
        $course= $this->entityManager->getRepository(Course::class)->findAll();
        // Fin tableaux

        // Add user
        $temporaryPassword= $passwordGenerator->passwordAleatoire(20);
        $user = new User();
        $formUser = $this->createForm(RegisterType::class, $user);
        $formUser->handleRequest($request);

        if ($formUser->isSubmitted() && $formUser->isValid()) {

            $user = $formUser->getData();
            $file = $formUser->get('picture')->getData();
            // Ajout de photo
            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = '.' . $file->guessExtension();
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . $extension;

                try {
                   
                    $file->move($this->getParameter('user_picture'), $newFilename);      
                    $user->setPicture($newFilename);
                } catch (FileException $exception) {
                    // Code à executer si une erreur est attrapée
                }
                       
            } else { 
            $this->addFlash('warning', 'Les types de fichier autorisés sont : .jpeg / .png' /* Autre fichier autorisé*/); 
            return $this->redirectToRoute('register'); 
        } 
        // Fin ajout photo

    $user->setPassword($this->passwordHasher->hashPassword($user, $temporaryPassword));
    $this->entityManager->persist($users);
    $this->entityManager->flush();

    $this->mailjet->sendEmail($user, 'Bienvenue Chez SCHOOLENT! Voici votre mot de passe temporaire :'   .$temporaryPassword);
    $this->addFlash('message_success', 'Votre ajout a bien été pris en compte, un mail a été envoyé!');
    //Message de succès
    return $this->redirectToRoute('dashboard');
}


        return $this->render('administration/admin/view_all.html.twig', [
            'users' => $users,
            'programmingLanguages' => $programmingLanguages,
            'sessions' => $sessions,
            'calendars' => $calendars,
            'formUser' => $formUser->createView(),
            'course' => $course,
        ]);
        // Fin add user
    }



     /**
     * @Route("/admin/edit/user/{id}", name="edit_user")
     */
    public function editUser($id, Request $request, SluggerInterface $slugger): Response
    {

        $user = $this->entityManager->getRepository(User::class)->find($id);

        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('picture')->getData();

            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = '.' . $file->guessExtension();
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . $extension;

                try {
                   
                    $file->move($this->getParameter('user_picture'), $newFilename);      
                    $user->setPicture($newFilename);
                } catch (FileException $exception) {
                    // Code à executer si une erreur est attrapée
                }
                       
            } else { 
                    $this->addFlash('warning', 'Les types de fichier autorisés sont : .jpeg / .png' /* Autre fichier autorisé*/); 
                    return $this->redirectToRoute('addUser'); 
                }


            $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return $this->redirect($request->get('redirect') ?? '/admin/view-users');
        }

        return $this->render('administration/admin/edit_users.html.twig', [

            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/delete/user/{id}", name="delete_user")
     */
    public function deleteUser(User $user, Request $request): Response
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->redirect($request->get('redirect') ?? '/admin/view-users');
    }

    /**
     * @Route("/admin/view-technologie", name="view-techno")
     */
    public function view_technologies(): Response
    {
        $technologies = $this->entityManager->getRepository(ProgrammingLanguage::class)->findAll();

        return $this->render('administration/admin/view_technologies.html.twig', [
            'technologies' => $technologies,

        ]);
    }

    /**
     * @Route("/admin/add/technologie", name="add_techno")
     */
    public function addTechnoLanguage(Request $request, SluggerInterface $slugger): Response
    {

        $techno = new ProgrammingLanguage();

        $form = $this->createForm(AddProgrammingLanguageType::class, $techno);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $technoPicture = $form->get('picture')->getData();

            if ($technoPicture) {
                $originalFilename = pathinfo($technoPicture->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $technoPicture->guessExtension();

                try {
                    $technoPicture->move(
                        $this->getParameter('techno_picture'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $techno->setPicture($newFilename);
            }
           
            $this->entityManager->persist($techno);
            $this->entityManager->flush();
            $this->addFlash('success', 'Nouvelle technologie ajoutée !');
            return $this->redirect($request->get('redirect') ?? '/admin/view-technologie');
            
        }

        return $this->render('administration/admin/add_technologies.html.twig', [
            'form' => $form->createView(),

        ]);
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
                $originalFilename = pathinfo($technoPicture->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $technoPicture->guessExtension();

                // Move the file to the directory where avatars are stored
                try {
                    $technoPicture->move(
                        $this->getParameter('techno_picture'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                if ($form->get('picture')->getData()) {
                    $technologie->setPicture($newFilename);
                }
                
            }

            $this->entityManager->persist($technologie);
            $this->entityManager->flush();
            $this->addFlash('success', 'Nouvelle technologie modifiée !');
            return $this->redirect($request->get('redirect') ?? '/admin/view-technologie');
        }

        return $this->render('administration/admin/edit_technologies.html.twig', [

            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/delete/technologie/{id}", name="delete_technologie")
     */
    public function deleteTehnologie(ProgrammingLanguage $technologie, Request $request): Response
    {
        $this->entityManager->remove($technologie);
        $this->entityManager->flush();

        $this->addFlash('success', 'La technologie a été suprimmée !');
        return $this->redirect($request->get('redirect') ?? '/admin/view-technologie');
    }

    /**
     * @Route("/admin/view-calendar", name="view-calendar")
     */
    public function viewCalendar(): Response
    {

        $calendar = $this->entityManager->getRepository(Calendar::class)->findBy([], ['id' => 'DESC']);

        return $this->render('administration/admin/view-calendar.html.twig', [
            'calendars' => $calendar,

        ]);
    }

    /**
     * @Route("/admin/add/calendar", name="add_calendar")
     */
    public function addcalendar(Request $request): Response
    {

        $calendar = new Calendar();

        $form = $this->createForm(CalendarType::class, $calendar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $calendar->setCreatedAt(new DateTimeImmutable());

            $this->entityManager->persist($calendar);
            $this->entityManager->flush();
            $this->addFlash('success', 'Nouvelle date ajoutée !');
            return $this->redirect($request->get('redirect') ?? '/admin/view-calendar');
            
        }

        return $this->render('administration/admin/add_calendar.html.twig', [
            'form' => $form->createView(),

        ]);
    }

    /**
     * @Route("/admin/edit/calendar/{id}", name="edit_calendar",methods={"GET|POST"})
     */
    public function editCalendar($id, Request $request): Response
    {

        $calendar = $this->entityManager->getRepository(Calendar::class)->findBy(['id' => $id]);
        $student = $this->entityManager->getRepository(User::class)->findBy([], ['role' => 'ROLE_USER']);

        $form = $this->createForm(EditCalendarType::class, $calendar[0]);
        $form->handleRequest($request);

        // dd($calendar);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->get('teacher')->getData();
            $newTechnologie = $form->get('category')->getData()->getName();
            $newStart = $form->get('start')->getData();
            $newSession = $form->get('session')->getData()->getName();
            $student = $form->get('session')->getData();
            $newEnd = $form->get('end')->getData();

            // dd($student);

            $this->entityManager->persist($calendar[0]);
            $this->entityManager->flush();
            $this->mailjet->sendEmail($user, "Votre planning vient d'etre mis à jour. Nouvelle intervention sur " . $newTechnologie . " du : " . date_format($newStart, 'd-m-y') . " Au " . date_format($newEnd, 'd-m-y.') . " Numero de session " . $newSession . ".");
            $this->mailjet->sendEmail($student, "Voici votre convocation pour le cours " . $newTechnologie . " du : " . date_format($newStart, 'd-m-y') . " Au " . date_format($newEnd, 'd-m-y.') . " Avec le formateur ");
            $this->addFlash('success', 'Calendrier modifié !');
            return $this->redirect($request->get('redirect') ?? '/admin/view-calendar');
        }

        return $this->render('administration/admin/edit-calendar.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/delete/calendar/{id}", name="delete_calendar")
     */
    public function deleteCalendar(Calendar $calendar, Request $request): Response
    {
        $this->entityManager->remove($calendar);
        $this->entityManager->flush();

        $this->addFlash('success', 'La date a été suprimmée');
        return $this->redirect($request->get('redirect') ?? '/admin/view-calendar');
    }

    /**
     * @Route("/admin/view-sessions", name="view-sessions")
     */
    public function viewSessions(): Response
    {
        $session = $this->entityManager->getRepository(Session::class)->findAll();

        return $this->render('administration/admin/view-session.html.twig', [
            'sessions' => $session,
        ]);
    }

    /**
     * @Route("/admin/add/session", name="add-session")
     */
    public function addSession(Request $request): Response
    {

        $session = new Session();

        $form = $this->createForm(SessionType::class, $session);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $session->setCreatedAt(new DateTimeImmutable());

            $this->entityManager->persist($session);
            $this->entityManager->flush();
            return $this->redirect($request->get('redirect') ?? '/admin/view-sessions');
            $this->addFlash('success', 'Nouvelle session ajoutée !');
        }

        return $this->render('administration/admin/add-session.html.twig', [
            'form' =>  $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/session/{id}/edit", name="edit-session",methods={"GET|POST"})
     */
    public function editSession(Session $session, Request $request): Response
    {
        $form = $this->createForm(EditSessionType::class, $session);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($session);
            $this->entityManager->flush();
            return $this->redirect($request->get('redirect') ?? '/admin/view-sessions');
            $this->addFlash('success', 'La session a été modifiée !');
        }

        return $this->render('administration/admin/edit-session.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/delete/session/{id}", name="delete-session")
     */
    public function deleteSession(Session $session, Request $request): Response
    {
        $this->entityManager->remove($session);
        $this->entityManager->flush();

        $this->addFlash('success', 'La session a été suprimmée');
        return $this->redirect($request->get('redirect') ?? '/admin/view-sessions');
    }

    /**
     * @Route("/admin/add/cours", name="add-cours")
     */
    public function addCourse(Request $request, SluggerInterface $slugger): Response
    {

        $course = new Course();

        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $courseFile = $form->get('link')->getData();

            if ($courseFile) {
                $originalFilename = pathinfo($courseFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $courseFile->guessExtension();

                try {
                    $courseFile->move(
                        $this->getParameter('cours_picture'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $course->setLink($newFilename);
            }

            $this->entityManager->persist($course);
            $this->entityManager->flush();
            return $this->redirect($request->get('redirect') ?? '/admin/view-cours');
            $this->addFlash('success', 'Nouveaux cours ajouté !');
        }

        return $this->render('administration/admin/add-cours.html.twig', [
            'form' =>  $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/delete/cours/{id}", name="delete-cours",methods={"GET"})
     */
    public function clearCourse(Course $Course, Request $request): Response
    {
        $this->entityManager->remove($Course);
        $this->entityManager->flush();

        return $this->redirect($request->get('redirect') ?? '/admin/view-cours');
    }
}
