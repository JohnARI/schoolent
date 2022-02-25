<?php

namespace App\Controller;

use DateTime;
use App\Entity\Calendar;
use App\Repository\CalendarRepository;
use Doctrine\ORM\EntityManagerInterface;
use PDOException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
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
};
