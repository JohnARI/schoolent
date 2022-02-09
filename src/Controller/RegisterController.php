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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{
    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {

        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }
    /**
     * @Route("/admin/register", name="register")
     */
    public function register(Request $request, UserRepository $userRepository): Response
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
                    $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();

                    return $this->redirectToRoute('login');
                }
                return $this->render('register/register.html.twig', [
                    'form' => $form->createView(),
                    'admins' => $admins,
                    'teachers' => $teachers,
                    'students' => $students,
                ]);
                break;
        }
    }
}
