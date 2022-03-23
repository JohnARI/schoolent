<?php

namespace App\Controller;

use DatePeriod;
use DateInterval;
use App\Entity\Calendar;
use App\Form\CalendarType;
use App\Repository\CalendarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TestController extends AbstractController
{

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        
    }

    /**
     * @Route("/test", name="test")
     */
    public function index(CalendarRepository $calendar): Response
    {

        $calendrier = $calendar->findAll();
        $query = $this->entityManager->createQuery(
            'SELECT c
                FROM App:Calendar c
            WHERE c.title != :title
            ORDER BY c.title ASC'
        )->setParameter('title', 'indisponible');

        $calendar = $query->getResult();

        $calendars = new Calendar;
        $form = $this->createForm(CalendarType::class, $calendars);

        //------------------------------------------------TEST----------------------------------------------
        
        // $moninfo = 'Test10 Testo';

        // $query = $this->entityManager->createQuery(
        //     'SELECT u.id
        //         FROM App:User u
        //     WHERE u.fullname = :fullname
        //     ORDER BY u.id ASC'
        // )->setParameter('fullname', $moninfo);

        // $id = $query->getSingleScalarResult();

        // settype($id, 'integer');
  
        // dd($id);

         //------------------------------------------------TEST----------------------------------------------

        return $this->render('test/test.html.twig',[

            'calendar' => $calendar,
            'form'=> $form->createView(),
            'calendrier'=> $calendrier
        ]);
        
    }


    /**
     * @Route("/test/delete/{id}", name="test_delete")
     */
    public function delete(Calendar $calendar, CalendarRepository $calendars, EntityManagerInterface $em, Request $request, $id): Response
    {
    
        $donnees = json_decode($request->getContent());


        $calendar->getId();
        $user = $this->getUser(); 
        $id_user = $this->getUser('id');
       

        $calendars->findBy(['id'=>$id]);

        $em->remove($calendar);
        $em->flush();
        
       

        $calendar = $calendars->findAll();

        // dd($events);

      
    


        return $this->render('test/test.html.twig', [

            'calendar' => $calendar,
        ]);

    }



    /**
     * @Route("/test2", name="test2", methods={"GET", "POST"})
     */
    public function index2(CalendarRepository $calendar, Request $request): Response
    {

        $newData = json_decode($request->getContent());
        $start = new \DateTime('2022-02-18 00:00:00') ;
        $end = new \DateTime('2022-03-21 00:00:00') ;
        $interval = DateInterval::createFromDateString('1 day');
        $daterange = new DatePeriod($start,$interval,$end);
        
       $test = new \DateTime('2022-02-28 00:00:00');

       $monTest = $test->format('Y-m-d');
        // dd($daterange);

        foreach($daterange as $date1){

            echo $date1->format('Y-m-d').'<br>';

            $date[] = $date1->format('Y-m-d');



            
         }

        //  echo '<pre>'; print_r($date); echo '</pre>';
         echo '<br>';
         echo '<br>';

        //  echo '<pre>'; print_r($daterange); echo '</pre>';

        //  dd($date);

         if(in_array($monTest, $date, false)){

            echo 'Vous etes bien dans l\'interval';

        }else{

            echo 'Vous n\'etes pas dans l\'interval';
        };
         
         // reverse the array
         
        //  $daterange = array_reverse(iterator_to_array($daterange));
                  
        //  foreach($daterange as $date1){
        //     echo $date1->format('Y-m-d').'<br>';
        //  }

     

        dd($newData);

        return $this->render('test/test2.html.twig');


    }

}