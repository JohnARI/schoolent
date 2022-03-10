<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Contact;
use App\Form\ContactType;
use App\Entity\EmailModel;
use App\Service\EmailSender;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/contact")
 */
class ContactController extends AbstractController
{
    /**
     * @Route("/", name="contact_new", methods={"GET", "POST"})
    */
    public function new(Request $request, EntityManagerInterface $entityManager, EmailSender $emailSender): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($contact);
            $entityManager->flush();

            // Envoi d'email
            $user = (new User())
                    ->setEmail('maisongaultier78@gmail.com')
                    ->setFirstName('SchoolEnt')
                    ->setLastname('School');

            $email = (new EmailModel())
            ->setTitle("Hello ".$user->getFirstname())
            ->setSubject("New contact from your website")
            ->setContent("<br>From : ".$contact->getEmail()
                        ."<br> Name : ".$contact->getName()
                        ."<br> Subject : ".$contact->getObjet()
                        ."<br><br>".$contact->getMessage());

            $emailSender->sendEmailNotificationByMailjet($user, $email);

            $contact = new Contact();
            $form = $this->createForm(ContactType::class, $contact);
            $this->addFlash('contact_success', 'Votre message a bien été envoyé, un administrateur va vous répondre très bientôt!');
            //Message de succès
        }

        if ($form->isSubmitted() && !$form->isValid()) {

            $this->addFlash('contact_error', 'Votre formulaire contient des erreurs, merci de bien vouloir les rectifier');
        }

        return $this->renderForm('contact/_form.html.twig', [
            'contact' => $contact,
            'form' => $form,
        ]);
    }
}
