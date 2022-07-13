<?php


namespace App\Service;

use App\Entity\Contact;
use Mailjet\Client;
use App\Entity\User;
use Twig\Environment;
use Mailjet\Resources;

class Mailjet
{
    private $twig;
    private $mailJetKey;
    private $mailJetKeySecret;

    public function __construct(Environment $twig,$mailJet_api_key, $mailJet_api_key_secret)
    {
        $this->twig = $twig;
        $this->mailJetKey = $mailJet_api_key;
        $this->mailJetKeySecret = $mailJet_api_key_secret;
    }

    public function sendEmail(User $user, string $myMessage)
    {
        $message = $this->twig->render('models/message.html.twig', [
            'user' => $user,
            'message' => $myMessage
        ]);

        $this->send($this->generateSingleBody($user, "Unlock Technologie", $message));
    }

    private function generateSingleBody(User $user, string $subject, string $message): array
    {
        return [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "a.takabait@gmail.com",
                        'Name' => "Unlock Technologie"
                    ],
                    
                    'To' => [
                        [
                            'Email' => $user->getEmail(),
                            'Name' => $user->getFirstname()
                        ]
                    ],
                    'TemplateID' => 3600624,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'body' => $message,
                    ]

                ]
            ]
        ];
    }

    public function sendEmailContact(Contact $contact, string $myMessage)
    {
        $message = $this->twig->render('models/contact.html.twig', [
            'contact' => $contact,
            'message' => $myMessage
        ]);

        $this->send($this->generateSingleBodyContact($contact, "Unlock Technologie", $message));
    }

    private function generateSingleBodyContact(Contact $contact, string $subject, string $message): array
    {
        return [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "a.takabait@gmail.com",
                        'Name' => "Unlock Technologie"
                    ],
                    
                    'To' => [
                        [
                            'Email' => $contact->getEmail(),
                            'Name' => $contact->getName()
                        ]
                    ],
                    'TemplateID' => 3600624,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'body' => $message,
                    ]

                ]
            ]
        ];
    }

    /**
     * Envoi de l'Email avec Mailjet
     * @param array $body
     */
    private function send(array $body): void
    { 
        $mj = new Client($this->mailJetKey, $this->mailJetKeySecret, true, ['version' => 'v3.1']);
      
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();
    }

}