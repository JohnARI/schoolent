<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Calendar;
use App\Form\CalendarType;
use App\Repository\UserRepository;
use App\Repository\CalendarRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/teacher/calendar")
 */
class CalendarController extends AbstractController
{

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->users = new ArrayCollection();
    }
    /**
     * @Route("/", name="calendar_index", methods={"GET","POST"})
     * @param Calendar $calendar
     * @return Response
     */
    public function index(CalendarRepository $calendarRepository,Request $request, EntityManagerInterface $entityManager): Response
    {
        $calendar = new Calendar();
        $form = $this->createForm(CalendarType::class, $calendar);
        $form->handleRequest($request);
        $id_user = $this->getUser('id');
        $teacher_calendar = $calendarRepository->findBy(['teacher_id'=>$id_user]);
        
        if ($this->isGranted('ROLE_TEACHER')){



            if ($form->isSubmitted() && $form->isValid()) {

                // dd($calendar);
                $entityManager->persist($calendar);
                $entityManager->flush();
    
                return $this->redirectToRoute('calendar_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('calendar/index.html.twig', [
                'calendars' => $teacher_calendar,
                'form' => $form->createView()
            ]);


        }
        
        
        return $this->render('calendar/index.html.twig', [
            'calendars' => $calendarRepository->findAll(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/new", name="calendar_new", methods={"GET", "POST"})
     * @param Calendar $calendar
     * @return Response
     * 
     */
    public function new(Request $request, EntityManagerInterface $entityManager, CalendarRepository $calendarRepository): Response
    {
        $calendar = new Calendar();
        $form = $this->createForm(CalendarType::class, $calendar);
        $form->handleRequest($request);
        $user = $this->getUser(); 
        $id_user = $this->getUser('id');

        $events = $calendarRepository->findBy(['teacher_id'=>$id_user]);

        // foreach($events as $event){

        //     $start = $event->getStart();
        //     $end = $event->getEnd();
            
        // }
      
        // // $interval = DateInterval::createFromDateString('1 day');
        // // $daterange = new DatePeriod($start,$interval,$end);
        // $start_date = $form['start']->getData();
        // $end_date = $form['end']->getData();
       

        // dd($daterange);


        if ($form->isSubmitted() && $form->isValid()) {

            // dd($calendar);
            $entityManager->persist($calendar);
            $entityManager->flush();

            return $this->redirectToRoute('calendar_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('calendar/_form.html.twig', [
            'calendar' => $calendar,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="calendar_show", methods={"GET"})
     * 
     */
    public function show(Request $request, CalendarRepository $calendar, UserRepository $userRepository, UserRepository $userRepo, $id): Response
    {

        
        $user = $this->getUser('id'); 
        $calendars = new Calendar;
        $form = $this->createForm(CalendarType::class, $calendars,[
                'action'=>$this->generateUrl('calendar_new'),
                'method'=>'POST'
        ]);
        $code = 201;
        $calendrier = $calendar->findAll();
       

        if ($this->isGranted('ROLE_TEACHER')){

               

            

            $calendars = new Calendar;
            $form = $this->createForm(CalendarType::class, $calendars,[
                'action'=>$this->generateUrl('calendar_new'),
                'method'=>'POST'
            ]);
            $form->handleRequest($request);
            $calendar = $calendar->findBy(['teacher_id'=>$user]);

            // dd($events);

            // $booking = [];
            // foreach ($events as $event) {

            //     $booking[] = [
            //         'id' => $event->getId(),
            //         'start' => $event->getStart()->format('Y-m-d'),
            //         'end' => $event->getEnd()->format('Y-m-d'),
            //         'title' => $event->getTitle(),
            //         'description' => $event->getDescription(),
            //         'session' => $event->getSession(),
            //         'backgroundColor' => $event->getBackgroundColor(),
            //     ];
            // }

            if ($form->isSubmitted() && $form->isValid()) {

                // dd($calendar);
                $this->entityManager->persist($calendar);
                $this->entityManager->flush();
    
                return $this->redirectToRoute('calendar_index', [], Response::HTTP_SEE_OTHER);
            }
    


            return $this->render('calendar/show.html.twig',[

                'calendar' => $calendar,
                'form'=> $form->createView(),
                'calendary'=> $calendrier,
                'code'=>$code,
            ]);

        }
        
        if ($this->isGranted('ROLE_ADMIN')){

            $events = $calendar->findBy(['teacher_id'=>$id]);
            $form->handleRequest($request);
            $calendrier = $calendar->findAll();

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


            return $this->render('calendar/show.html.twig',[

                'calendar' => $events,
                'form'=> $form->createView(),
                'calendary'=> $calendrier,
                'code'=> $code
            ]);

        }

        return $this->redirectToRoute('calendar_show');
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

        return $this->redirectToRoute('calendar_index', [], Response::HTTP_SEE_OTHER);
    }
}
