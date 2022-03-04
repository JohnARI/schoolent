<?php

namespace App\Controller;

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

        return $this->render('test/test2.html.twig',[

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

      
    


        return $this->render('test/test2.html.twig', [

            'calendar' => $calendar,
        ]);

    }

}
