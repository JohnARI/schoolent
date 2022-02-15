<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Entity\ProgrammingLanguage;
use App\Entity\Session;
use App\Entity\User;
use App\Form\AddProgrammingLanguageType;
use App\Form\CalendarType;
use App\Form\EditCalendarType;
use App\Form\EditProgrammingLanguageType;
use App\Form\EditUserType;
use App\Service\Mailjet;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;
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
    public function index(): Response
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();

        return $this->render('administration/admin/view_user.html.twig', [
            'users' => $users,
        ]);
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
     * @Route("/admin/view-sessions", name="view-sessions")
     */
    public function viewSessions(): Response
    {
        $session = $this->entityManager->getRepository(Session::class)->findBy([], ['id' => 'DESC']);

        return $this->render('administration/admin/view-session.html.twig', [
            'sessions' => $session,
        ]);
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

        $form = $this->createForm(EditCalendarType::class, $calendar[0]);
        $form->handleRequest($request);

        // dd($calendar);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->get('teacher')->getData();
            $newTechnologie = $form->get('category')->getData()->getName();
            $newStart = $form->get('start')->getData();
            $newSession = $form->get('session')->getData()->getName();
            $newEnd = $form->get('end')->getData();

            $this->entityManager->persist($calendar[0]);
            $this->entityManager->flush();
            $this->mailjet->sendEmail($user, "Votre planning vient d'etre mis à jour. Nouvelle intervention sur " . $newTechnologie . " du : " . date_format($newStart, 'd-m-y') . " Au " . date_format($newEnd, 'd-m-y.') . " Numero de session " . $newSession . ".");
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
}
