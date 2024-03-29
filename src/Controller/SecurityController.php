<?php

namespace App\Controller;

use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use App\Service\Mailjet;
use App\Service\PasswordGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private $passwordEncoder;
    private $doctrine;
    private $mailjet;

    public function __construct(UserPasswordHasherInterface $passwordEncoder, Mailjet $mailjet, EntityManagerInterface $doctrine)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->mailjet = $mailjet;
        $this->doctrine = $doctrine;
    }
    /**
     * @Route("/login", name="login")
    */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
            $this->addFlash(
                'danger',
                'Vous êtes déjà connecté !'
            );
             return $this->redirectToRoute('default');
             
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/reset-password", name="reset-password")
    */
    public function resetPassword(Request $request, UserRepository $userRepository, PasswordGenerator $passwordGenerator): Response
    {

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);
        $temporaryPassword = $passwordGenerator->passwordAleatoire(10);
        

        if ($form->isSubmitted() && $form->isValid()) {

            $email = $form->get('email')->getData();
            $user = $userRepository->findByEmail($email);

            // dd($user);

            if ($user) {
                $user[0]->setPassword(
                    $this->passwordEncoder->hashPassword($user[0], $temporaryPassword)
                );

                $em = $this->doctrine;
                $em->persist($user[0]);
                $em->flush();

                $this->mailjet->sendEmail($user[0], "Voici votre nouveau de mot passe temporaire : "   . $temporaryPassword . " Pensez à le modifier lors de la prochaine connexion sur votre espace personnel.");
                $this->addFlash('success', 'Un email vient d\' être envoyé à cette adresse !');
            } else {
                $this->addFlash('erreur', 'Ce compte n\'existe pas !');
            }
        }

        return $this->render('security/reset-password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
