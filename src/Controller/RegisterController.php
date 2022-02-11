<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Mailjet;
use App\Form\RegisterType;
use App\Repository\UserRepository;
use App\Service\PasswordGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{
    private $mailjet;
    



    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, Mailjet $mailjet )
    {

        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->mailjet = $mailjet;
        
    }
    /**
     * @Route("administration/admin/add-user", name="addUser")
     */
    public function register(Request $request, UserRepository $userRepository, PasswordGenerator $passwordGenerator, SluggerInterface $slugger): Response
    {
        $roles = $this->getUser()->getRole();
        $temporaryPassword= $passwordGenerator->passwordAleatoire(20);
        switch ($roles) {
            case "Formateur":
                return $this->redirectToRoute('login');
                break;

            case "Eleve":
                return $this->redirectToRoute('login');
                break;

            case $this->isGranted('ROLE_USER') == false:
                return $this->redirectToRoute('login');
                break;
                
            case "Administrateur":
                $admins =   $userRepository->findByRole('ROLE_ADMIN');
                $teachers = $userRepository->findByRole('ROLE_TEACHER');
                $students = $userRepository->findByRole('ROLE_USER');

                $user = new User();
                $form = $this->createForm(RegisterType::class, $user);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {

                    $user = $form->getData();
                    $file = $form->get('picture')->getData();

                    if ($file) {
                        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $extension = '.' . $file->guessExtension();
                        $safeFilename = $slugger->slug($originalFilename);
                        $newFilename = $safeFilename . '-' . uniqid() . $extension;
        
                        try {
                           
                            $file->move($this->getParameter('user_picture'), $newFilename);      
                            $user->setPicture($newFilename);
                        } catch (FileException $exception) {
                            // Code à executer si une erreur est attrapée
                        }
                               
                    } else { 
                    $this->addFlash('warning', 'Les types de fichier autorisés sont : .jpeg / .png' /* Autre fichier autorisé*/); 
                            return $this->redirectToRoute('register'); 
                        }

                    $user->setPassword($this->passwordHasher->hashPassword($user, $temporaryPassword));
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();

                    $this->mailjet->sendEmail($user, 'Bienvenue Chez SCHOOLENT! Voici votre mot de passe temporaire :'   .$temporaryPassword);
                    return $this->redirectToRoute('dashboard');
                }

                return $this->render('administration/admin/add_users.html.twig', [
                    'form' => $form->createView(),
                    'admins' => $admins,
                    'teachers' => $teachers,
                    'students' => $students,
                ]);
                
                break;
        }

        return $this->redirectToRoute('login');
    }
}
