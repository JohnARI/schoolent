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
      
        // $interval = DateInterval::createFromDateString('1 day');
        // $daterange = new DatePeriod($start,$interval,$end);
        $start_date = $form['start']->getData();
        $end_date = $form['end']->getData();
       

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
     * @param Calendar $calendar
     * @return Response
     * 
     */
    public function show(Request $request, CalendarRepository $calendar, UserRepository $userRepository, $id): Response
    {

        
        $user = $this->getUser(); 
        $id = $this->getUser('id');
        

        if ($this->isGranted('ROLE_TEACHER')) {

               

            

            $calendars = new Calendar;
            $form = $this->createForm(CalendarType::class, $calendars);
            $form->handleRequest($request);
            $calendrier = $calendar->findAll();
            $calendar = $calendar->findBy(['teacher_id'=>$id]);

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
                'calendrier'=> $calendrier
            ]);

        }else{

            $events = $calendar->findBy(['id'=>$id]);

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


            return $this->render('test/test.html.twig',[

                'calendar' => $calendar,
                'form'=> $form->createView(),
                'calendrier'=> $calendrier
            ]);

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

        return $this->redirectToRoute('calendar_index', [], Response::HTTP_SEE_OTHER);
    }
}
