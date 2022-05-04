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
    public function index(?Calendar $calendarr, CalendarRepository $calendar, Request $request): Response
    {

        $donnees = json_decode($request->getContent());
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

        

        

        if($donnees){

        $calendarr->setStart(new \Datetime($donnees->start));
        $calendarr->setEnd(new \DateTime($donnees->end));
        $start = $calendarr->getStart();
        $end = $calendarr->getEnd();
        $interval = DateInterval::createFromDateString('1 day');
        $daterange = new DatePeriod($start,$interval,$end);
        
    //    $test = new \DateTime('2022-02-28 00:00:00');

    //    $monTest = $test->format('Y-m-d');
        dd($donnees);

        


        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('c.start','c.end')
        ->from(Calendar::class, 'c');

        $query = $queryBuilder->getQuery();

        $calendar = $query->getResult();

        

        $dateInit = array_rand($calendar,1);
        $dateSqlInit = $calendar[$dateInit]['start'];
        $dateSqlInit->format('Y-m-d');
        $startDate = $dateSqlInit->format('Y-m-d H:i:s');

        $dateFin = array_rand($calendar,1);
        $dateSqlFin = $calendar[$dateFin]['end'];
        $dateSqlFin->format('Y-m-d');
        $endDate = $dateSqlFin->format('Y-m-d H:i:s');

        echo $startDate;
        echo '<br>';
        echo $endDate;
        
        echo '<br>';
        echo '<br>';


        // $input = array("Neo", "Morpheus", "Trinity", "Cypher", "Tank");
        // $rand_keys = array_rand($input, 2);
        // echo $input[$rand_keys[2]] . "\n";
        // echo $input[$rand_keys[1]] . "\n";

        foreach($daterange as $date2){

            // echo $date2->format('Y-m-d H:i:s').'<br>';

            $date[] = $date2->format('Y-m-d H:i:s');
    
         }

        //  echo '<pre>'; print_r($date); echo '</pre>';
        //  echo '<br>';
        //  echo '<br>';

        // echo '<pre>'; print_r($calendar); echo '</pre>';

        //  dd($date);

         if(in_array($startDate, $date, false) || in_array($endDate, $date, false)){

            echo 'Vous etes bien dans l\'interval';

            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder->select('c.teacher_name')
                ->from(Calendar::class, 'c')
                ->add('where', "c.start not IN ( :date) OR c.end not IN ( :date)")
                ->setParameter('date', $date);

                $query = $queryBuilder->getQuery();

                $calendario = $query->getResult();

                // echo '<pre>'; print_r($calendario); echo '</pre>';

                return $this->render('test/test.html.twig',[

                    'calendar' => $calendar,
                    'form'=> $form->createView(),
                    'calendrier'=> $calendario
                ]);

        }else{

            echo 'Vous n\'etes pas dans l\'interval';

            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder->select('c.teacher_name')
            ->from(Calendar::class, 'c');

            $query = $queryBuilder->getQuery();

            $calendario = $query->getResult();


            // echo '<pre>'; print_r($calendario); echo '</pre>';
        }

        return $this->render('test2/test.html.twig');//,[

    //         'calendar' => $calendar,
    //         'form'=> $form->createView(),
    //         'calendrier'=> $calendario,
    //     ]);
        
    };
         

         //------------------------------------------------TEST----------------------------------------------

       


        return $this->render('test/test.html.twig',[

            'calendar' => $calendar,
            'form'=> $form->createView(),
            'calendrier'=> $calendrier,
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
     * @Route("/test2", name="test2", methods={"PUT"})
     */
    public function index2(?Calendar $calendarr, CalendarRepository $calendar, Request $request): Response
    {

         //On instancie une notification
        $calendarr = new Calendar;

        $donnees = json_decode($request->getContent());
        dd($donnees);
        $calendarr->setStart(new \Datetime($donnees->start));
        $calendarr->setEnd(new \DateTime($donnees->end));
        $start = $calendarr->getStart();
        $end = $calendarr->getEnd();
        $interval = DateInterval::createFromDateString('1 day');
        $daterange = new DatePeriod($start,$interval,$end);
        
       $test = new \DateTime('2022-02-28 00:00:00');

       $monTest = $test->format('Y-m-d');
        //dd($donnees);

        


        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('c.start','c.end')
        ->from(Calendar::class, 'c');

        $query = $queryBuilder->getQuery();

        $calendar = $query->getResult();

        

        $dateInit = array_rand($calendar,1);
        $dateSqlInit = $calendar[$dateInit]['start'];
        $dateSqlInit->format('Y-m-d');
        $startDate = $dateSqlInit->format('Y-m-d H:i:s');

        $dateFin = array_rand($calendar,1);
        $dateSqlFin = $calendar[$dateFin]['end'];
        $dateSqlFin->format('Y-m-d');
        $endDate = $dateSqlFin->format('Y-m-d H:i:s');

        echo $startDate;
        echo '<br>';
        echo $endDate;
        
        echo '<br>';
        echo '<br>';


        // $input = array("Neo", "Morpheus", "Trinity", "Cypher", "Tank");
        // $rand_keys = array_rand($input, 2);
        // echo $input[$rand_keys[2]] . "\n";
        // echo $input[$rand_keys[1]] . "\n";

        foreach($daterange as $date2){

            echo $date2->format('Y-m-d H:i:s').'<br>';

            $date[] = $date2->format('Y-m-d H:i:s');
    
         }

        //  echo '<pre>'; print_r($date); echo '</pre>';
         echo '<br>';
         echo '<br>';

        // echo '<pre>'; print_r($calendar); echo '</pre>';

        //  dd($date);

         if(in_array($startDate, $date, false) || in_array($endDate, $date, false)){

            echo 'Vous etes bien dans l\'interval';

            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder->select('c.teacher_name')
                ->from(Calendar::class, 'c')
                ->add('where', "c.start not IN ( :date) OR c.end not IN ( :date)")
                ->setParameter('date', $date);

                $query = $queryBuilder->getQuery();

                $calendario = $query->getResult();

                echo '<pre>'; print_r($calendario); echo '</pre>';

        }else{

            echo 'Vous n\'etes pas dans l\'interval';

            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder->select('c.teacher_name')
            ->from(Calendar::class, 'c');

            $query = $queryBuilder->getQuery();

            $calendario = $query->getResult();


            echo '<pre>'; print_r($calendario); echo '</pre>';
        };
         

         // reverse the array
         
        //  $daterange = array_reverse(iterator_to_array($daterange));
                  
        //  foreach($daterange as $date1){
        //     echo $date1->format('Y-m-d').'<br>';
        //  }

     echo '<br>';
     echo '<br>';

        
// $input = array("Neo", "Morpheus", "Trinity", "Cypher", "Tank");
// $rand_keys = array_rand($input, 2);
// echo $input[$rand_keys[2]] . "\n";
// echo $input[$rand_keys[1]] . "\n";

            


        return $this->render('test/test2.html.twig');


    }

}