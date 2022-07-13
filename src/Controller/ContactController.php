<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Service\Mailjet;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact_new", methods={"GET", "POST"})
    */
    public function new(Request $request, EntityManagerInterface $entityManager, Mailjet $mailjet): Response
    {
        
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($contact);
            $entityManager->flush();

            $mailjet->sendEmailContact($contact, 'Votre message a bien été envoyé, Un administrateur va vous répondre très bientôt!');

            $this->addFlash('contact_success', 'Votre message a bien été envoyé, Un administrateur va vous répondre très bientôt!');
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
