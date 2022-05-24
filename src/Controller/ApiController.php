<?php

namespace App\Controller;

use DateTime;
use DatePeriod;
use DateInterval;
use App\Entity\User;
use App\Entity\Calendar;
use App\Form\CalendarType;
use App\Repository\UserRepository;
use App\Repository\CalendarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProgrammingLanguageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin")
 */
class ApiController extends AbstractController
{

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        
    }
    /**
     * @Route("/api/{id}/edit", name="api_even_edit", methods={"PUT"})
     */
    public function majEvent(?Calendar $calendar, Request $request, EntityManagerInterface $em, CalendarRepository $calendars,ProgrammingLanguageRepository $programe): Response
    { //Potentiellement un objet Calendar


        //On Récupère les données
        $donnees = json_decode($request->getContent());
        $category = $programe->findAll();

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

        return $this->render('calendar_api/index.html.twig');
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


            $dateStart = new Datetime($donnees->start);
            // On Hydrate l'objet avec les données
            $calendar->setTitle($donnees->title);
            $calendar->setStart(date_add($dateStart, date_interval_create_from_date_string("0 day")));
            $calendar->setEnd(new DateTime($donnees->end));
            $calendar->setDescription($donnees->description);
            $calendar->setTeacherName($donnees->teacherName);
           
          

            $moninfo = $donnees->teacherName;

            $maCategorie = $donnees->categorie;

            if($moninfo){

    

                    $query = $this->entityManager->createQuery(
                        'SELECT u.id
                            FROM App:User u
                        WHERE u.fullname = :fullname
                        ORDER BY u.id ASC'
                    )->setParameter('fullname', $moninfo);

               $id = $query->getSingleScalarResult();

                //    $id = $query->getSingleResult();
                    //$id = $query->getResult();

                    settype($id, 'integer');
            
                

                $calendar->setTeacherId($id);
            }


            if($maCategorie){


                $query = $this->entityManager->createQuery(
                    'SELECT p.id
                        FROM App:ProgrammingLanguage p
                    WHERE p.name = :myname'
                )->setParameter('myname', $maCategorie);


                $categorie = $query->getSingleResult();

                settype($categorie, 'integer');

                

            }


                $calendar->setCategory($categorie);

                



                if( $donnees->categorie == 'Html'){
                    $calendar->setBackgroundColor('#EE1581');
                }elseif( $donnees->category == 'PHP'|| $donnees->title == 'php'|| $donnees->title == 'Php'){
                    $calendar->setBackgroundColor('#6C1D89');
                }elseif( $donnees->category == 'SQL'|| $donnees->title == 'sql'|| $donnees->title == 'Sql'){
                    $calendar->setBackgroundColor('#2ABAD7');
                }elseif( $donnees->category == 'CSS'|| $donnees->title == 'css'|| $donnees->title == 'Css'){
                    $calendar->setBackgroundColor('#D7632A');
                }elseif( $donnees->category == 'JAVASCRIPT'|| $donnees->title == 'javascript'|| $donnees->title == 'Javascript'){
                    $calendar->setBackgroundColor('#F2F21A');
                }elseif( $donnees->category == 'BOOSTRAP'|| $donnees->title == 'boostrap'|| $donnees->title == 'Boostrap'){
                    $calendar->setBackgroundColor('#9C6F9C');
                }elseif( $donnees->category == 'SYMFONY'|| $donnees->title == 'symfony'|| $donnees->title == 'Symfony'){
                    $calendar->setBackgroundColor('#8A828A');
                }elseif( $donnees->category == 'REACT'|| $donnees->title == 'react'|| $donnees->title == 'React'){
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
    public function majEventDelete(?Calendar $calendarDelete, Request $request, CalendarRepository $calendars, ProgrammingLanguageRepository $programe, UserRepository $user,$id): Response
    { //Potentiellement un objet Calendar

        
        $this->entityManager->remove($calendarDelete);
        $this->entityManager->flush();


            

            $query = $this->entityManager->createQuery(
                'SELECT c
                    FROM App:Calendar c
                WHERE c.title != :title
                ORDER BY c.title ASC'
            )->setParameter('title', 'indisponible');
            $calendar = $query->getResult();
            $category = $programe->findAll();
            $userTeacher = $user->findBy(['isTeacher'=>1]);
            $calendarr = new Calendar;
            $form = $this->createForm(CalendarType::class, $calendarr);

            $code = 201;
            
    


            return $this->render('calendar_api/capi.html.twig',[

                'calendar' => $calendar,
                'teacher'=> $userTeacher,
                'form'=> $form->createView(),
                'category'=>$category,
                'code'=>$code,
            ]);


    }


    /**
     * @Route("/api", name="api", methods={"GET", "POST"})
     */
    public function index(?Calendar $calendarr, CalendarRepository $calendary, Request $request,ProgrammingLanguageRepository $programe, UserRepository $user): Response
    {

        // $url = $request->query->get('donnees');
          
        // $urlStart = substr($url,10,24);
        // $urlEnd = substr($url,43,-2);

        // $test = array($urlStart,$urlEnd);
    

        // if(!isset($url)){

        // return $this->redirect($this->generateUrl('test',$test));

        // }

        // $test = 

        // dd($test);

        

        $category = $programe->findAll();

        $query = $this->entityManager->createQuery(
            'SELECT c.start, c.end
                FROM App:Calendar c
            WHERE c.title = :title 
            ORDER BY c.title ASC'
        )->setParameter('title', 'indisponible');
        $indisponible = $query->getResult();
        $calendar = $calendary->findAll();
        $calendars = new Calendar;
        $form = $this->createForm(CalendarType::class, $calendars);
        $userTeacher = $user->findBy(['isTeacher'=>1]);
        $code="";//initialisation
        $cookieStart = 1;
        $cookieEnd = 2;
        $cookieAllDay = 0;
        
     

        //dd($indisponible);
        // if($request->isXmlHttpRequest()) {
        //     var_dump("ok");
        //     $test = $request->request->get('donnees');
        // }

        
        // $test = $_COOKIE['start
        // '];

        // if(!empty($test)){

        //     dd($test);

        // }

        
       
        $routerName = $request->getRequestUri();

        // dd($routerName);


        // header("Content-Type: text/plain");

        $urlStart = (isset($_GET["start"])) ? $_GET["start"] : NULL;
        $urlEnd = (isset($_GET["end"])) ? $_GET["end"] : NULL;
        $cookie = (isset($_COOKIE["start"])) ? $_COOKIE["start"] : NULL;


       
        
        if ($urlStart && $urlEnd){


            header('Location: '.$routerName);
            // $routerName = $request->getRequestUri();

           
           
        

            /** Je met ma requête ajax(GET) dans une variable :
             * Etant donnée qu'ell est en string() je délimite la bout concernant a START
             * Et le bout concernant END
             */
            // $url = $_GET['donnees'];
            $urlStart = substr($urlStart,1,-1);
            $urlEnd = substr($urlEnd,1,-1);
            $start = new \DateTime($urlStart);
            $end = new \DateTime($urlEnd);
            $interval = DateInterval::createFromDateString('1 day');
            $daterange = new DatePeriod($start,$interval,$end);

            // dd($daterange);

            /**
             * L'ensemble de mes dates de la BDD
             * Je vais choisir des dates aléatoires à partir de la BDD
             * 
             */

            if(!empty($cookie)){

                $cookieStart = $_COOKIE['start'];
                $cookieEnd = $_COOKIE['end'];
                $cookieAllDay = $_COOKIE['all'];

                $debut = new \DateTime($urlStart);
                $debut = date_format($debut,"d/m/Y");

                $fin = new \DateTime($urlEnd);
                $fin = date_format($fin,"d/m/Y");

                $this->addFlash('contact_success', 'liste des enseignants mise à jour du ' .$debut.' au '.$fin);
                
                }
            
            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder->select('c.start,c.end')
            ->from(Calendar::class, 'c');
            $query = $queryBuilder->getQuery();
            $maBddDates = $query->getResult();

            $dateInit = array_rand($maBddDates,1);
            $dateSqlInit = $maBddDates[$dateInit]['start'];
            $dateSqlInit->format('Y-m-d');
            $startDate = $dateSqlInit->format('Y-m-d H:i:s');

            $dateFin = array_rand($maBddDates,1);
            $dateSqlFin = $maBddDates[$dateFin]['end'];
            $dateSqlFin->format('Y-m-d');
            $endDate = $dateSqlFin->format('Y-m-d H:i:s');




            

            //Mon intervalle de date issue de la BDD

            //Debut interval
            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder->select('c, MIN(c.start)')
            ->from(Calendar::class, 'c');
            $query = $queryBuilder->getQuery();
            $minStartBdd = $query->getResult();
            $DateS = $minStartBdd[0][1];
            $DateDebut = new \DateTime($DateS);
        

            //Fin interval
            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder->select('c, MAX(c.end)')
            ->from(Calendar::class, 'c');
            $query = $queryBuilder->getQuery();
            $maxEndBdd = $query->getResult();
            $DateE = $maxEndBdd[0][1];
            $DateFin = new \DateTime($DateE);

        
            //Délimitation de l'interval à partir de DateDebut et DateFin
            $zoneDateBdd = DateInterval::createFromDateString('1 day');
            $intervalBdd = new DatePeriod($DateDebut,$zoneDateBdd,$DateFin);

        

        
            foreach($intervalBdd as $zoneDate){

                // echo $zoneDate->format('Y-m-d H:i:s').'<br>';

                //mettre mon interval de dates BDD dans un tableau

            $intervalBddNew[] = $zoneDate->format('Y-m-d H:i:s');
            }


                // dd($intervalBddNew);

                /**
                 * Verifier si les dates selectionnées dans le calendrier sont dans notre intevalle de dates BDD
                 */
                if(($start >= $intervalBddNew || $start <=  $intervalBddNew ) || ($end >= $intervalBddNew || $end <= $intervalBddNew)){

                        $code = 200;

            


                            foreach($daterange as $newTest){
                    
                            
                                
                            $newTest->format('Y-m-d H:i:s');//J'inspecte les dates du select(daterange) et je m'assure de bien y être
                    
                            $dateTest[] = $newTest->format('Y-m-d H:i:s');

                            }
                            
                    
                            $queryBuilder = $this->entityManager->createQueryBuilder();
                            $queryBuilder->select('c.start','c.end','c.teacher_name')
                            ->from(Calendar::class, 'c')
                            ->add('where', "c.start IN ( :date) OR c.end IN ( :date)")
                            ->setParameter('date', $dateTest);
                    
                            $query = $queryBuilder->getQuery();
                            $calendar1 = $query->getResult();

                           

                            /**"array_rand" sélectionne une ou plusieurs valeurs au hasard dans un tableau et retourne la ou les clés de ces valeurs. 
                             * Cette fonction utilise un pseudo générateur de nombre aléatoire, 
                             * ce qui ne convient pas pour de la cryptographie. */

                            if(!empty($calendar1)){


                            $dateInit = array_rand($calendar1,1);
                            $dateSqlInit = $calendar1[$dateInit]['start'];
                            $dateSqlInit->format('Y-m-d');
                            $startDate = $dateSqlInit->format('Y-m-d H:i:s');

                            $dateFin = array_rand($calendar1,1);
                            $dateSqlFin = $calendar1[$dateFin]['end'];
                            $dateSqlFin->format('Y-m-d');
                            $endDate = $dateSqlFin->format('Y-m-d H:i:s');

                            echo $startDate;
                            echo '<br>';
                            echo $endDate;

                            }




                            //($startDate >= $date && $startDate <=  $date ) || ($endDate >= $date && $end <= $date)
                            // if(in_array($startDate, $dateTest, false) || in_array($endDate, $dateTest, false)){

                            // echo 'Vous etes bien dans l\'interval';
                                    $code = 200;

                                    // dd($date);

                                        $queryBuilder = $this->entityManager->createQueryBuilder();
                                        $queryBuilder->select('u.fullname')
                                            ->from(User::class, 'u')
                                            ->add('where', "u.is_teacher = 1")
                                            ->Leftjoin('App\Entity\Calendar', 'c', 'WITH', 'c.teacher_name = u.fullname')
                                            ->add('where', "c.start not IN ( :date) AND c.end not IN ( :date)")
                                            ->setParameter('date', $dateTest)
                                            ->groupBy('u.fullname');
                                            

                                            $query = $queryBuilder->getQuery();

                                            $calendario = $query->getResult();

                                            // echo '<pre>'; print_r($calendario); echo '</pre>';


                                                    if(($startDate <= $indisponible && $startDate >=  $indisponible ) && ($endDate <= $indisponible && $end >= $indisponible)){

                                                        //JE VERIFIE SI "INDISPONIBLE" se trouve dans la zone sélectionnée et j'élimine les formateurs indisponibles
                                                        
                                                        $code = 203;

                                                        $dateIndispoD = array_rand($indisponible,1);
                                                        $date_i = $indisponible[$dateIndispoD]['start'];
                                                        $startIndispo = $date_i->format('Y-m-d H:i:s');
                                                        $startIndispo = new \DateTime($startIndispo);
                                                    


                                                        $dateIndispoF = array_rand($indisponible,1);
                                                        $date_f = $indisponible[$dateIndispoF]['end'];
                                                        $endIndispo = $date_f->format('Y-m-d H:i:s');
                                                        $endIndispo = new \DateTime($endIndispo);

                                                        $zoneInterIndispo = DateInterval::createFromDateString('1 day');
                                                        $dateIndispoRange = new DatePeriod($startIndispo,$zoneInterIndispo,$endIndispo);


                                                        foreach( $dateIndispoRange as $indispoDate){
                    
                            
                                
                                                            $indispoDate->format('Y-m-d H:i:s');//J'inspecte les dates du select(daterange) et je m'assure de bien y être
                                                    
                                                            $indispo[] = $indispoDate->format('Y-m-d H:i:s');
                                
                                                            }


                                                        $queryBuilder = $this->entityManager->createQueryBuilder();
                                                        $queryBuilder->select('u.fullname')
                                                            ->from(User::class, 'u')
                                                            ->add('where', "u.is_teacher = 1")
                                                            ->Leftjoin('App\Entity\Calendar', 'c', 'WITH', 'c.teacher_name = u.fullname')
                                                            ->add('where', "c.start not IN ( :date) OR c.end not IN ( :date) AND c.title != :title")
                                                            ->setParameters(array(
                                                                'date'=>$indispo,
                                                                'title'=>'indisponible'
                                                            ))
                                                            ->groupBy('u.fullname');
                        

                                                        $query = $queryBuilder->getQuery();
                                                            
                                                        $T_indisponible = $query->getResult();
                                                            
                                                        return $this->render('calendar_api/capi.html.twig',[

                                                            'calendar' => $calendar,
                                                            'form'=> $form->createView(),
                                                            'indisponible'=> $T_indisponible,
                                                            'code'=>$code,
                                                            'cookieStart'=>$cookieStart,
                                                            'cookieEnd'=>$cookieEnd,
                                                            'cookieAllDay'=>$cookieAllDay,
                                                            'category'=>$category,
                                                        ]);




                                                    }


                                

                                            return $this->render('calendar_api/capi.html.twig',[

                                                'calendar' => $calendar,
                                                'form'=> $form->createView(),
                                                'calendrier'=> $calendario,
                                                'code'=>$code,
                                                'cookieStart'=>$cookieStart,
                                                'cookieEnd'=>$cookieEnd,
                                                'cookieAllDay'=>$cookieAllDay,
                                                'category'=>$category,
                                            ]);

                }
                else
                {

                            $code = 201;


                            // echo '<pre>'; print_r($calendario); echo '</pre>';

                            return $this->render('calendar_api/capi.html.twig',[

                                'calendar' => $calendar,
                                'form'=> $form->createView(),
                                'teacher'=> $userTeacher,
                                'code'=>$code,
                                'cookieStart'=>$cookieStart,
                                'cookieEnd'=>$cookieEnd,
                                'cookieAllDay'=>$cookieAllDay,
                                'category'=>$category,
                            ]);
                        

                }

         //------------------------------------------------TEST----------------------------------------------

        }
        else
        {


            
            


            $code = 201;

            return $this->render('calendar_api/capi.html.twig',[

                'calendar' => $calendar,
                'form'=> $form->createView(),
                'code'=>$code,
                'teacher'=> $userTeacher,
                'cookieStart'=>$cookieStart,
                'cookieEnd'=>$cookieEnd,
                'cookieAllDay'=>$cookieAllDay,
                'category'=>$category,
                
            ]);



            // $url = $t1;
            // $urlStart = substr($url,10,24);
            // $urlEnd = substr($url,43,-2);
            // $test = array($urlStart,$urlEnd);
          
            // $url2 = $this->generateUrl('test',$test);
            
           
            // dd($url2);


            // return $this->redirect($this->generateUrl('test',$test));




        }

        


        return $this->redirectToRoute('api');
        // unset($_GET['donnees']);
    
    }








}

