<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Grade;
use DateTimeImmutable;
use App\Entity\Session;
use App\Service\Mailjet;
use App\Form\EditProfilType;
use App\Form\GradeProfilType;
use App\Form\EditProfilPasswordType;
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
        $editUserPasswordForm = $this->createForm(EditProfilPasswordType::class, $user);
        $editUserForm->handleRequest($request);
        $editUserPasswordForm->handleRequest($request);

        if ($editUserPasswordForm->isSubmitted() && $editUserPasswordForm->isValid()) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', 'Utilisateur mis à jour avec succès');
            return $this->redirect($request->getUri());
        }


        if ($editUserForm->isSubmitted() && $editUserForm->isValid()) {
            $this->addFlash('success', 'Utilisateur mis à jour avec succès');
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirect($request->getUri());
        }

        return $this->render('profil/profil.html.twig', [

            'editUserForm' => $editUserForm->createView(),
            'editUserPasswordForm' => $editUserPasswordForm->createView(),

        ]);
    }

    /**
     * @Route("/profil/{id}", name="profil_user")
     */
    public function profilUser($id, Request $request): Response
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        $grades = $this->entityManager->getRepository(Grade::class)->findBy(['user' => $user]);
        $session = $this->entityManager->getRepository(Session::class)->findAll();
        $mySession = $this->getUser()->getSession($session);
        $myId = $this->getUser();
        $grade = new Grade();

        $formGrade = $this->createForm(GradeProfilType::class, $grade);
        $formGrade->handleRequest($request);

        if ($formGrade->isSubmitted() && $formGrade->isValid()) {
            $grade->setCreatedAt(new DateTimeImmutable());
            $grade->setTeacher($myId);
            $grade->setSession($mySession);
            $grade->setUser($user);
            $this->entityManager->persist($grade);
            $this->entityManager->flush();
            $this->addFlash('success', 'La note a été attribué');
            return $this->redirect($request->getUri());
        }



        return $this->render('profil/profil_user.html.twig', [
            'user' => $user,
            'grades' => $grades,
            'formGrade' => $formGrade->createView(),


        ]);
    }
}
