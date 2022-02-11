<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class RegisterController extends AbstractController
{
    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {

        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }
    /**
     * @Route("/admin/add-user", name="addUser")
     */
    public function addUser(Request $request, UserRepository $userRepository, SluggerInterface $slugger): Response
    {
        $roles = $this->getUser()->getRole();

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
                            return $this->redirectToRoute('addUser'); 
                        }

                    $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();

                    $this->mailjet->sendEmail($user, 'Bienvenue Chez SCHOOLENT! Voici votre mot de passe temporaire :'   .$temporaryPassword);
                    $this->redirectToRoute('home');
                }
                return $this->render('administration/admin/add_users.html.twig', [
                    'form' => $form->createView(),
                    'admins' => $admins,
                    'teachers' => $teachers,
                    'students' => $students,
                ]);
                break;
        }
    }
}
