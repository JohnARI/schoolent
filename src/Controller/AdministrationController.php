<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Session;
use App\Form\EditUserType;
use App\Entity\ProgrammingLanguage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdministrationController extends AbstractController
{

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }
    

    /**
     * @Route("/admin/view-users", name="view-users")
     */
    public function viewUsers(): Response
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();

        return $this->render('administration/admin/view_user.html.twig', [
            'users' => $users,

        ]);
    }

       /**
     * @Route("/admin/view-all", name="view-all")
     */
    public function viewAll(): Response
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();
        $programmingLanguages = $this->entityManager->getRepository(ProgrammingLanguage::class)->findAll();
        $sessions = $this->entityManager->getRepository(Session::class)->findAll();

        return $this->render('administration/admin/view_all.html.twig', [
            'users' => $users,
            'programmingLanguages' => $programmingLanguages,
            'sessions' => $sessions,
            

        ]);
    }

         /**
     * @Route("/admin/edit/user/{id}", name="edit_user")
     */
    public function editUser($id, Request $request, SluggerInterface $slugger): Response
    {

        $user = $this->entityManager->getRepository(User::class)->find($id);

        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

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
            return $this->redirect($request->get('redirect') ?? '/admin/view-users');
        }

        return $this->render('administration/admin/edit_users.html.twig', [

            'form' => $form->createView()
        ]);
    }

        /**
     * @Route("/admin/delete/user/{id}", name="delete_user")
     */
    public function deleteUser(User $user, Request $request): Response
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->redirect($request->get('redirect') ?? '/admin/view-users');
    }

}
