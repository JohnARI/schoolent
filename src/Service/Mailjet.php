<?php


namespace App\Service;



use Mailjet\Client;
use App\Entity\User;
use Twig\Environment;
use Mailjet\Ressources;

class Mailjet
{
    private $twig;
    private $mailJetKey = "1652e89bbf90f5a98865561327f1f3de";
    private $mailJetKeySecret ="92b982984a66e272cc93c9f068b293f6";

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
        // $this->mailJetKey = $mailJet_api_key;
        // $this->mailJetKeySecret = $mailJet_api_key_secret;
    }

    public function sendEmail(User $user, string $myMessage)
    {
        $message = $this->twig->render('models/message.html.twig', [
            'user' => $user,
            'message' => $myMessage
        ]);

        $this->send($this->generateSingleBody($user, "School ENT", $message));
    }

    private function generateSingleBody(User $user, string $subject, string $message): array
    {
        return [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "a.takabait@gmail.com",
                        'Name' => "School"
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

    /**
     * Envoi de l'Email avec Mailjet
     * @param array $body
     */
    private function send(array $body): void
    {
       
        $mj = new Client($this->mailJetKey, $this->mailJetKeySecret, true, ['version' => 'v3.1']);
      
        $response = $mj->post(Ressources::$Email, ['body' => $body]);
        $response->success();
    }

}