<?php

use Mailjet\Resources;

class Mail
{
    private string $apiKey = 'e097582ef7b0787999d3a3e7afaf5aa1';
    private string $secret = 'e34d9df97fc72b1d510d4bf76b62ea04';

    public function sendMail(string $email,string $username, string $verify): void
    {
        $mj = new Mailjet\Client($this->apiKey, $this->secret);

        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $domaine = $_SERVER['HTTP_HOST'];
        $url = $protocol . $domaine;
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "devyourwebsite@gmail.com",
                        'Name' => "DevYourWebsite"
                    ],
                    'To' => [
                        [
                            'Email' => $email,
                            'Name' => $username
                        ]
                    ],
                    'Subject' => "Confirmation de votre compte",
                    'TemplateID' => 4983982,
                    'Variables' => [
                        'content' => $username,
                        'hostname' => $url,
                        'verify' => $verify
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        var_dump($response->success());
    }
}