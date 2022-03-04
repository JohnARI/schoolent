<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Calendar;
use App\Form\CalendarType;
use App\Repository\UserRepository;
use App\Repository\CalendarRepository;
use DateInterval;
use DatePeriod;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Proxies\__CG__\App\Entity\User as EntityUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/calendar")
 */
class CalendarController extends AbstractController
{


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->users = new ArrayCollection();
    }
    /**
     * @Route("/", name="calendar_index", methods={"GET"})
     */
    public function index(CalendarRepository $calendarRepository): Response
    {
        return $this->render('calendar/index.html.twig', [
            'calendars' => $calendarRepository->findAll(),
        ]);
    }


    /**
     * @Route("/view", name="calendar_view", methods={"GET"})
     */
    public function view(): Response
    {
        return $this->render('calendar/new.html.twig');
    }


    /**
     * @Route("/new", name="calendar_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, CalendarRepository $calendarRepository): Response
    {
        
        $calendar = new Calendar();
        $form = $this->createForm(CalendarType::class, $calendar);
        $form->handleRequest($request);
        $user = $this->getUser(); 
        $id_user = $this->getUser('id');

        $events = $calendarRepository->findBy(['teacher_id'=>$id_user]);

        foreach($events as $event){

            $start = $event->getStart();
            $end = $event->getEnd();
        }
      
        $interval = DateInterval::createFromDateString('1 day');
        // $daterange = new DatePeriod($start,$interval,$end);
        $start_date = $form['start']->getData();
        $end_date = $form['end']->getData();
       

        // dd($daterange);


        if ($form->isSubmitted() && $form->isValid()) {

           

            // $start_date = $events->getStart();
            // $end_date = $calendarRepository->getEnd();
           

           
            // dd($start_date);
           

        if($start_date < $start && $end_date < $start || $start_date > $end && $end_date > $end ){

               
            
            $entityManager->persist($calendar);
             $entityManager->flush();

             $this->addFlash('message_succès', 'Votre évènement a bien été enrégistré');

             return $this->redirectToRoute('calendar_index', [], Response::HTTP_SEE_OTHER);

               





         }else{
            
    
            
        

             $this->addFlash('message_error', 'Les dates sélectionnées contienent déjà un évènememnt');


             return $this->renderForm('calendar/_form.html.twig', [
                'calendar' => $calendar,
                'form' => $form,
            ]);

         }

        }

        return $this->renderForm('calendar/_form.html.twig', [
            'calendar' => $calendar,
            'form' => $form,
        ]);
        
    }



    /**
     * @Route("/{id}", name="calendar_show", methods={"GET"})
     */
    public function show(CalendarRepository $calendar, UserRepository $userRepository, $id): Response
    {

        $admins =   $userRepository->findByRole('ROLE_ADMIN');
        $teachers = $userRepository->findByRole('ROLE_TEACHER');
        
        $user = $this->getUser(); 
        $id_user = $this->getUser('id');
        

        if ($this->isGranted('ROLE_TEACHER')) {

               
            
            $events = $calendar->findBy(['teacher_id'=>$id_user]);

            // dd($events);

            $booking = [];
            foreach ($events as $event) {

                $booking[] = [
                    'id' => $event->getId(),
                    'start' => $event->getStart()->format('Y-m-d'),
                    'end' => $event->getEnd()->format('Y-m-d'),
                    'title' => $event->getTitle(),
                    'description' => $event->getDescription(),
                    'session' => $event->getSession(),
                    'backgroundColor' => $event->getBackgroundColor(),
                    
                ];
            }

            // dd($booking);

            $data = json_encode($booking);

            

        } else {


            $events = $calendar->findBy(['teacher_id'=>$id]);

            // dd($events);

            $booking = [];
            foreach ($events as $event) {

                $booking[] = [
                    'id' => $event->getId(),
                    'start' => $event->getStart()->format('Y-m-d'),
                    'end' => $event->getEnd()->format('Y-m-d'),
                    'title' => $event->getTitle(),
                    'description' => $event->getDescription(),
                    'session' => $event->getSession(),
                    'backgroundColor' => $event->getBackgroundColor(),
                ];
            }


            $data = json_encode($booking);

        }

        return $this->render('calendar/show.html.twig', compact('data'));
    }

    /**
     * @Route("/{id}/edit", name="calendar_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Calendar $calendar, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CalendarType::class, $calendar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('calendar_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('calendar/edit.html.twig', [
            'calendar' => $calendar,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="calendar_delete", methods={"POST"})
     */
    public function delete(Request $request, Calendar $calendar, EntityManagerInterface $entityManager): Response
    {
    

        if ($this->isCsrfTokenValid('delete' . $calendar->getId(), $request->request->get('_token'))) {
            $entityManager->remove($calendar);
            $entityManager->flush();
        }

        

        return $this->redirectToRoute('test');
    }


    
}
