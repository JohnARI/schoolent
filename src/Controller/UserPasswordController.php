<?php

namespace App\Controller;

use App\Form\UserPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPasswordController extends AbstractController
{
    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager) 
    {  
        $this->entityManager = $entityManager; 
        $this->passwordHasher = $passwordHasher;   
    }
    /**
     * @Route("/student/password_reset", name="user_password")
     */
    public function passwordReset(Request $request): Response
    {
        $user = $this->getUser(); 
        $form = $this->createForm(UserPasswordType::class, $user); 
        $form->handleRequest($request); 
        
        if($form->isSubmitted() && $form->isValid()){  
            
            // $oldPswd = 
            $newPwd = $form->get('password')->getData(); 

            // if ($newPwd->isPasswordValid($user, $oldPswd)) {

            $user->setPassword(
                $this->passwordHasher->hashPassword($user, $newPwd) 
            );

            $this->entityManager->persist($user);
            $this->entityManager->flush();


            $this->addFlash('success',' votre mot passe a été modifié!');
            return $this->redirectToRoute('login');
        // } else {
        //     $form->addError(new FormError('Ancien mot de passe incorrect'));
        // }
    }

        return $this->render('student/user_password.html.twig', [
            'form' => $form->createView(),  
        ]);
    }
}