<?php

namespace App\Controller;

use App\Repository\CalendarRepository;
use App\Service\PasswordGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/test", name="test")
     */
    public function index(CalendarRepository $calendar): Response
    {

        $event = $calendar->findAll();
    
        // $events = $calendar->findAll();

        // // dd($events);

        // $booking = [];
        // foreach($events as $event){

        //     $booking[] = [
        //     'id' => $event->getId(),
        //     'start' => $event->getStart()->format('Y-m-d'),
        //     'end' => $event->getEnd()->format('Y-m-d'),
        //     'title' => $event->getTitle(),
        //     'description' => $event->getDescription(),
        //     'session' => $event->getSession(),
        //     'backgroundColor' =>$event->getBackgroundColor(),
        //     ];
        // }

        // $data = json_encode($booking);

        return $this->render('test/test.html.twig', [

            'calendar'=>$event,
        ]);
        
    }

}

