<?php declare(strict_types=1);

namespace simplerest\core\libs;

use PDO;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

use GuzzleHttp;
use simplerest\libs\SendinBlue\Client\Configuration;
use simplerest\libs\SendinBlue\Client\Model\SendSmtpEmail;
use simplerest\libs\SendinBlue\Client\Api\AccountApi;
use simplerest\libs\SendinBlue\Client\ObjectSerializer;
use simplerest\libs\SendinBlue\Client\Api\TransactionalEmailsApi;


class Mails 
{
    protected static $errors      = null; 
    protected static $status      = null; 
    protected static $options     = [];
    protected static $silent      = false;
    protected static $debug_level = null;
    protected static $mailer      = null;

    // change mailer
    static function setMailer(string $name){
        static::$mailer = $name;
    }

    static function getMailer(){
        global $config;
        return static::$mailer ?? $config['email']['mailer_default'];
    }

    static function errors(){
        return static::$errors;
    }

    static function status(){
        return (empty(static::$errors)) ? 'OK' : 'error';
    }

    // overide options
    static function config(Array $options){
        static::$options = $options;
    }

    static function silentDebug($level = null){
        global $config;

        $options = $config['email']['mailers'][ static::getMailer() ];

        if (isset($options['SMTPDebug']) && $options['SMTPDebug'] != 0){
            $default_debug_level = $options['SMTPDebug'];
        }

        $level = static::$debug_level ?? $level ?? $default_debug_level ?? 4;

        static::config([
            'SMTPDebug' => $level
        ]);

        static::$silent = true;
    }

    /*
        level 1 = client; will show you messages sent by the client
        level 2  = client and server; will add server messages, it’s the recommended setting.
        level 3 = client, server, and connection; will add information about the initial information, might be useful for discovering STARTTLS failures
        level 4 = low-level information. 
    */
    static function debug(int $level = 4){
        static::$debug_level = $level;
    }

    /*
        Usar una interfaz común para SMTP y correos via API

        Es preferible recibir $from y  $replyTo como arrays de la forma:
            
            [
                'name' => 'xxx',
                'email' => 'xxxxx@xxx.com'
            ]

        y $to como un array de arrays:

        [
            [
                'name' => 'xxx',
                'email' => 'xxxxx@xxx.com'
            ], 
            
            // ...
        ]

        Ver
        https://stackoverflow.com/questions/3149452/php-mailer-multiple-address
        https://stackoverflow.com/questions/24560328/phpmailer-altbody-is-not-working

        Gmail => habilitar:

        https://myaccount.google.com/lesssecureapps
    */
    static function sendMail(Array $to, $subject = '', $body = '', $alt_body = null, $attachments = null, Array $from = [], Array $cc = [], Array $bcc = [], Array $reply_to = []){
		$config = config();

        $body = trim($body);

        if (!Strings::startsWith('<html>', $body)){
            $body = "<html><body>$body</body></html>";
        }

        if (empty($subject)){
            throw new \Exception("Subject is required");
        }

        if (empty($body) && empty($alt_body)){
            throw new \Exception("Body or alt_body is required");
        }

        if (Arrays::is_assoc($to)){
            $to = [ $to ];
        }

        if (Arrays::is_assoc($cc)){
            $cc = [ $cc ];
        }

        if (Arrays::is_assoc($bcc)){
            $bcc = [ $bcc ];
        }

        // if (empty($reply_to)){
        //     $reply_to = $from;
        // }

        $mailer = static::getMailer();

		$mail = new PHPMailer();
        $mail->isSMTP();

        $options = array_merge($config['email']['mailers'][$mailer], static::$options);

        if (static::$debug_level !== null){
            $options['SMTPDebug'] = static::$debug_level;
        }

        foreach ($options as $k => $prop){
			$mail->{$k} = $prop;
        }	

        if (!empty($reply_to)){
            $mail->addReplyTo($reply_to['email'], $reply_to['name'] ?? '');
        }

        $mail->setFrom(
            $from_email ?? $config['email']['from']['address'] ?? $config['email']['mailers'][$mailer]['Username'], 
            $from_name  ?? $config['email']['from']['name']
        );

        if (!empty($from)){
            $mail->setFrom($from['email'], $from['name'] ?? '');
        }

        foreach ($to as $_to){
            $mail->addAddress($_to['email'], $_to['name'] ?? '');
        }

        $mail->Subject = $subject;
		$mail->msgHTML($body); 
		
		if (!is_null($alt_body)){
            $mail->AltBody = $alt_body;
        }
		
        if (!empty($attachments)){
            if (!is_array($attachments)){
                $attachments = [ $attachments ];
            }

            foreach($attachments as $att){
                $mail->addAttachment($att);    
            }
        }

        if (!empty($cc)){            
            foreach($cc as $_cc){
                $mail->addCC($_cc['email'], $_cc['name'] ?? '');
            }
        }

        if (!empty($bcc)){            
            foreach($bcc as $_bcc){
                $mail->addBCC($_bcc['email'], $_bcc['name'] ?? '');
            }
        }

        if (static::$silent){
            ob_start();
        }
		
        if (!$mail->send())
        {	
            static::$errors = $mail->ErrorInfo;

            if (static::$silent){
                Files::dump(static::$errors, 'dump.txt', true);
            }

            $ret = static::$errors;
        }else{
            if (static::$silent){
                Files::dump(true, 'dump.txt', true);
            }

            static::$errors = null;
            $ret =  true;
        }        
                 
        if (static::$silent){
            $content = ob_get_contents();
            ob_end_clean();
        }

        return $ret;
	}


    static function sendinblue(Array $to, $subject = '', $body = '', $alt_body = null, $attachments = null, Array $from = [], Array $cc = [], Array $bcc = [], Array $reply_to = []){
        $config = config();

        $body = trim($body);

        if (!Strings::startsWith('<html>', $body)){
            $body = "<html><body>$body</body></html>";
        }

        if (empty($subject)){
            throw new \Exception("Subject is required");
        }

        if (empty($body) && empty($alt_body)){
            throw new \Exception("Body or alt_body is required");
        }

        if (Arrays::is_assoc($to)){
            $to = [ $to ];
        }

        if (Arrays::is_assoc($cc)){
            $cc = [ $cc ];
        }

        if (Arrays::is_assoc($bcc)){
            $bcc = [ $bcc ];
        }

        $mailer = static::getMailer();

        $from['email'] = $from['email'] ?? $config['email']['from']['address'] ?? $config['email']['mailers'][$mailer]['Username'];
        $from['name']  = $from['name']  ?? $config['email']['from']['name'];

        if (empty($reply_to)){
            $reply_to = $from;
        }

        $credentials = Configuration::getDefaultConfiguration()->setApiKey('api-key', $config['sendinblue_api_key']);
        $apiInstance = new TransactionalEmailsApi(new GuzzleHttp\Client(),$credentials);

        $args = [ 
            'sender'  => [],
            'replyTo' => [],
            'to'      => [],
            'htmlContent' => null            
        ];

        if ($subject != null){
            $args['subject'] = $subject;
        }

        $args['sender']  = $from;        
        $args['to']      = $to;

        if (!empty($reply_to)){
            $args['replyTo'] = $reply_to;
        }

        if (!empty($cc)){
            $args['cc'] = $cc;
        }

        if (!empty($bcc)){
            $args['bcc'] = $bcc;
        }

        if ($body != null){
            $args['htmlContent'] = $body;
        }

        if ($alt_body != null){
            $args['params'] = $args['params'] ?? [];
            $args['params']['bodyMessage'] = $alt_body;
        }

        $sendSmtpEmail = new SendSmtpEmail($args);

        try {
            $result = $apiInstance->sendTransacEmail($sendSmtpEmail);

            if (static::$debug_level >0){
                if (static::$silent){
                    Files::dump(true, 'dump.txt', true);
                } else {
                    dd($result);
                }
            }
        } catch (\Exception $e) {
            static::$errors = $e;

            if (static::$debug_level >0){
                if (static::$silent){
                    Files::dump($e->getMessage(), 'dump.txt', true);
                } else {
                    dd($e->getMessage());
                }
            }
        }
    }
}