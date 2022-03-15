<?php

namespace App\Controller;

use DateTime;
use App\Entity\Calendar;
use App\Repository\UserRepository;
use App\Repository\CalendarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        
    }
    /**
     * @Route("/api/{id}/edit", name="api_even_edit", methods={"PUT"})
     */
    public function majEvent(?Calendar $calendar, Request $request, EntityManagerInterface $em, CalendarRepository $calendars): Response
    { //Potentiellement un objet Calendar


        //On Récupère les données
        $donnees = json_decode($request->getContent());

        if (
            isset($donnees->title) && !empty($donnees->title) &&
            isset($donnees->start) && !empty($donnees->start) &&
            isset($donnees->end) && !empty($donnees->start) &&
            isset($donnees->description) && !empty($donnees->description) &&
            isset($donnees->backgroundColor) && !empty($donnees->backgroundColor)
        ) {

            //les données sont complètes
            // On initialise un code
            $code = 200; //J'ai mis à jour

            //On vérifie sur l'id existe
            if (!$calendar) {

                //On instancie une notification
                $calendar = new Calendar;

                //On change le code
                $code = 201; //j'ai crée

            }

            // On Hydrate l'objet avec les données
            $calendar->setTitle($donnees->title);
            $calendar->setStart(new Datetime($donnees->start));
            $calendar->setEnd(new DateTime($donnees->end));
            $calendar->setDescription($donnees->description);
            $calendar->setBackgroundColor($donnees->backgroundColor);

            $em->persist($calendar);
            $em->flush();

            

            //On retourne le code
            return new Response('Ok', $code);

        } else {

            return new Response('Données incomplètes', 404);
        }

        return $this->render('test/index.html.twig');
    }


/**
     * @Route("/api/edit", name="api_event_edit", methods={"PUT"})
     */
    public function majEvents(?Calendar $calendar, Request $request, EntityManagerInterface $em, UserRepository $user): Response
    { //Potentiellement un objet Calendar


        $users = $user->findAll();

        $info = [];
            foreach ($user as $users) {

                $info[] = [
                    'id' => $users->getId(),
                    'fullname' => $users->getFullname(),
                ];
            }


        
        //On Récupère les données
        $donnees = json_decode($request->getContent());


        if (
            isset($donnees->title) && !empty($donnees->title) &&
            isset($donnees->start) && !empty($donnees->start) &&
            isset($donnees->end) && !empty($donnees->start) &&
            isset($donnees->description) && !empty($donnees->description) &&
            isset($donnees->backgroundColor) && !empty($donnees->backgroundColor)
        ) {

            //les données sont complètes
            // On initialise un code
            $code = 200; //J'ai mis à jour

            //On vérifie sur l'id existe
            if (!$calendar) {

                //On instancie une notification
                $calendar = new Calendar;

                //On change le code
                $code = 201; //j'ai crée

            }

            // On Hydrate l'objet avec les données
            $calendar->setTitle($donnees->title);
            $calendar->setStart(new Datetime($donnees->start));
            $calendar->setEnd(new DateTime($donnees->end), strtotime('+ 1 days'));
            $calendar->setDescription($donnees->description);
            $calendar->setTeacherName($donnees->teacherName);

            $moninfo = $donnees->teacherName;

            if($moninfo){

    

                    $query = $this->entityManager->createQuery(
                        'SELECT u.id
                            FROM App:User u
                        WHERE u.fullname = :fullname
                        ORDER BY u.id ASC'
                    )->setParameter('fullname', $moninfo);

                $id = $query->getSingleScalarResult();

                   //$id = $query->getSingleResult();
                    //$id = $query->getResult();

                    settype($id, 'integer');
            
                

                $calendar->setTeacherId($id);
            }

                if( $donnees->title == 'HTML'|| $donnees->title == 'html'|| $donnees->title == 'Html'){
                    $calendar->setBackgroundColor('#EE1581');
                }elseif( $donnees->title == 'PHP'|| $donnees->title == 'php'|| $donnees->title == 'Php'){
                    $calendar->setBackgroundColor('#6C1D89');
                }elseif( $donnees->title == 'SQL'|| $donnees->title == 'sql'|| $donnees->title == 'Sql'){
                    $calendar->setBackgroundColor('#2ABAD7');
                }elseif( $donnees->title == 'CSS'|| $donnees->title == 'css'|| $donnees->title == 'Css'){
                    $calendar->setBackgroundColor('#D7632A');
                }elseif( $donnees->title == 'JAVASCRIPT'|| $donnees->title == 'javascript'|| $donnees->title == 'Javascript'){
                    $calendar->setBackgroundColor('#F2F21A');
                }elseif( $donnees->title == 'BOOSTRAP'|| $donnees->title == 'boostrap'|| $donnees->title == 'Boostrap'){
                    $calendar->setBackgroundColor('#9C6F9C');
                }elseif( $donnees->title == 'SYMFONY'|| $donnees->title == 'symfony'|| $donnees->title == 'Symfony'){
                    $calendar->setBackgroundColor('#8A828A');
                }elseif( $donnees->title == 'REACT'|| $donnees->title == 'react'|| $donnees->title == 'React'){
                    $calendar->setBackgroundColor('#8BEF49');
                }else{
            $calendar->setBackgroundColor($donnees->backgroundColor);
                }

            $em->persist($calendar);
            $em->flush();

            

            //On retourne le code
            return new Response('Ok', $code);

        } else {

            return new Response('Données incomplètes', 404);
        }

        
        
    }

    /**
     * @Route("/api/{id}/delete", name="api_even_delete", methods={"PUT"})
     */
    public function majEventDelete(Calendar $calendarDelete, Request $request, CalendarRepository $calendar, $id): Response
    { //Potentiellement un objet Calendar

        
        $this->entityManager->remove($calendarDelete);
        $this->entityManager->flush();


            $calendrier = $calendar->findAll();
            $query = $this->entityManager->createQuery(
                'SELECT c
                    FROM App:Calendar c
                WHERE c.title != :title
                ORDER BY c.title ASC'
            )->setParameter('title', 'indisponible');
    
            $calendar = $query->getResult();
    
            $calendars = new Calendar;
            
    


            return $this->render('test/test.html.twig',[

                'calendar' => $calendar,
                'calendrier'=> $calendrier
            ]);


    }
}

