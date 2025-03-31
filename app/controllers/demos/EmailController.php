<?php

namespace Boctulus\Simplerest\Controllers\demos;

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Request;
use Boctulus\Simplerest\Core\Response;
use Boctulus\Simplerest\Core\Libs\Mail;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Libs\SendinBlue;
use Boctulus\Simplerest\Core\Controllers\Controller;

class EmailController extends Controller
{
    

    /*
        Habilitar:

        https://myaccount.google.com/lesssecureapps

        e IMAP

        https://www.arclab.com/en/kb/email/how-to-enable-imap-pop3-smtp-gmail-account.html
    */
    function sender()
    {
        // Mail::config([
        //     'SMTPDebug' => 4
        // ]);

        Mail::debug(4);
        //Mail::silentDebug();

        Mail::setMailer('pulque');

        Mail::send(
            [
                'email' => 'boctulus@gmail.com',
                'name' => 'Pablo'
            ],
            'Pruebita 001JRBX',
            'Hola!<p/>Esto es una más <b>prueba</b> con el server de JuamMa<p/>Chau'
        );

        dd(Mail::errors(), 'Error');
        dd(Mail::status(), 'Status');
    }

    function sender_o()
    {
        Mail::config([
            'Timeout' => 10
        ]);

        Mail::debug(4);
        //Mail::silentDebug();

        Mail::setMailer('pulque'); ///

        SendinBlue::send(
            [
                'email' => 'boctulus@gmail.com',
                'name' => 'Pablo'
            ],
            'Pruebita 001JRB',
            'Hola!<p/>Esto es una más <b>prueba</b> con el server de JuamMa<p/>Chau',
            // null, 
            // null,
            // [],
            // [
            //     [
            //         'email' => 'pulketo@gmail.com'
            //     ],
            //     [
            //         'email' => 'ing.mario.alberto@gmail.com',
            //         'name'  => 'Ing. PK Pulketo'
            //     ]
            // ]
        );

        dd(Mail::errors(), 'Error');
        dd(Mail::status(), 'Status');
    }

    // function sender_v8(){
    //     dd(
    //         Mail::sendMail(
    //             to_email:'boctulus@gmail.com', 
    //             subject:'Prueba B8',
    //             body:'HEY!!!!<p/>Esto es una más <b>prueba</b> con el SMTP de <i>Brimell</i><p/>Chau'
    //         )
    //     );     
    // }

    function sendinblue()
    {
        Mail::debug(1);

        $body = <<<BODY
        Ciao!

        Ci dedichiamo allo sviluppo di plugin per diverse piattaforme di eCommerce.
        
        In particolare, come sviluppatore PHP ho quasi 15 anni di esperienza (WordPress, Magento, ..., CodeIgniter, Laravel) e metto a disposizione i miei repository pubblici:
        
        https://github.com/botulus
        
        Ogni giorno costruisco un'ampia varietà di plugin per WordPress / WooCommerce: sincronizzazione negozi, preventivi, sistemi di autenticazione,...
        
        Sto aspettando qualsiasi richiesta.
        
        Atte.,
        
        Paolo Bozzolo
        info@solucionbinaria.com
        BODY;

        SendinBlue::send(
            [
                'email' => 'boctulus@gmail.com',
                'name' => 'Pablo'
            ],
            'Pruebita 001JRB XXX',
            Strings::paragraph($body),
            null,
            [],
            [
                [
                    'email' => 'pulketo@gmail.com'
                ],
                [
                    'email' => 'ing.mario.alberto@gmail.com',
                    'name'  => 'Ing. PK Pulketo'
                ]
            ]
        );
    }

    // function sendinblue_ori(){
    //     $api_key = "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";

    //     $credentials = Configuration::getDefaultConfiguration()->setApiKey('api-key', $api_key);
    //     $apiInstance = new TransactionalEmailsApi(new GuzzleHttp\Client(),$credentials);

    //     $sendSmtpEmail = new SendSmtpEmail([
    //         'subject' => 'from the PHP SDK!!!!!!!!!',
    //         'sender' => ['name' => 'Sendinblue', 'email' => 'noresponder@solucionbinaria.com'],
    //         'replyTo' => ['name' => 'Sendinblue', 'email' => 'noresponder@solucionbinaria.com'],
    //         'to' => [[ 'name' => 'PK Pulkes', 'email' => 'boctulus@gmail.com']],
    //         'htmlContent' => '<html><body><h1>This is a transactional email {{params.bodyMessage}}</h1></body></html>',
    //         'params' => ['bodyMessage' => 'made just for you!']
    //     ]);

    //     try {
    //         $result = $apiInstance->sendTransacEmail($sendSmtpEmail);
    //         dd($result);
    //     } catch (\Exception $e) {
    //         echo $e->getMessage(),PHP_EOL;
    //     }
    // }


    /*
        https://github.com/sendgrid/sendgrid-php
    */
    function sendgrid()
    {
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("boctulus@gmail.com", "boctulus");
        $email->setSubject("Probando SendGrid");
        $email->addTo("boctulus@gmail.com", "boctulus");
        $email->addContent("text/plain", "Probando el envio,...");
        $email->addContent(
            "text/html",
            "<strong>and easy to do anywhere, even with PHP</strong>"
        );
        $sendgrid = new \SendGrid(env('SENDGRID_API_KEY'));
        try {
            $response = $sendgrid->send($email);
            print $response->statusCode() . "\n";
            print_r($response->headers());
            print $response->body() . "\n";
        } catch (\Exception $e) {
            echo 'Caught exception: ' . $e->getMessage() . "\n";
        }
    }


}

