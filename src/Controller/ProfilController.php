<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditProfilType;
use App\Service\Mailjet;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfilController extends AbstractController
{

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, Mailjet $mailjet)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->mailjet = $mailjet;
    }
    
    /**
     * @Route("/profil", name="profil")
     */
    public function profil(Request $request): Response
    {
        $id = $this->getUser()->getId();
        $user = $this->entityManager->getRepository(User::class)->find($id);

        $editUserForm = $this->createForm(EditProfilType::class, $user);
        $editUserForm->handleRequest($request);

        if ($editUserForm->isSubmitted() && $editUserForm->isValid()) {
            $this->addFlash('success', 'Utilisateur mis à jour avec succès');
            $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            
            return $this->redirect($request->getUri());
        }

        return $this->render('profil/profil.html.twig', [

            'editUserForm' => $editUserForm->createView()
        ]);
    }
}