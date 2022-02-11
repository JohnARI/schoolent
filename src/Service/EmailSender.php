<?php
namespace App\Services;

use Mailjet\Client;
use App\Entity\User;
use Mailjet\Resources;
use App\Entity\EmailModel;


Class EmailSender{



    public function sendEmailNotificationByMailjet(User $user, EmailModel $email){

        $mj = new Client($_ENV["MJ_APIKEY_PUBLIC"], $_ENV["MJ_APIKEY_PRIVATE"],true,['version' => 'v3.1']);

        $body = [
          'Messages' => [
            [
              'From' => [
                'Email' => "maisongaultier78@gmail.com",
                'Name' => "Paul TOTI"
              ],
              'To' => [
                [
                  'Email' => $user->getEmail(),
                  'Name' => $user->getFirstname(),
                ]
              ],
              'TemplateID' => 3605948,
              'TemplateLanguage' => true,
              'Subject' => $email->getSubject(),
              'Variables' => [
                  'title' => $email->getTitle(),
                  'content' => $email->getContent()

              ]
            ]
          ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        // $response->success() && dd($response->getData());
      

    }




}