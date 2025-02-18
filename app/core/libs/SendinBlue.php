<?php

namespace simplerest\core\libs;

use GuzzleHttp;
use simplerest\core\interfaces\IMail;
use simplerest\core\libs\Logger;
use simplerest\core\libs\StdOut;
use simplerest\libs\SendinBlue\Client\Api\TransactionalEmailsApi;
use simplerest\libs\SendinBlue\Client\Configuration;
use simplerest\libs\SendinBlue\Client\Model\SendSmtpEmail;

class SendinBlue extends MailBase implements IMail
{
    static function send(Array $to, $subject = '', $body = '', $attachments = null, Array $from = [], Array $cc = [], Array $bcc = [], Array $reply_to = [], $alt_body = null) : bool {
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

        if (Arrays::isAssoc($to)){
            $to = [ $to ];
        }

        if (Arrays::isAssoc($cc)){
            $cc = [ $cc ];
        }

        if (Arrays::isAssoc($bcc)){
            $bcc = [ $bcc ];
        }

        $from['email'] = $from['email'] ?? $config['email']['from']['address'];
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

        $sendSmtpEmail = (SendSmtpEmail::class) (new SendSmtpEmail($args));

        try {
            $result = $apiInstance->sendTransacEmail($sendSmtpEmail);

            if (static::$debug_level >0){
                if (static::$silent){
                    Files::dump(true, 'dump.txt', true);
                } else {
                    StdOut::print($result);
                }
            }

            return true;
        } catch (\Exception $e) {
            static::$errors = $e;

            if (static::$debug_level >0){
                if (static::$silent){
                    Files::dump($e->getMessage(), null, true);
                } else {
                    StdOut::print($e->getMessage());
                }
            }

            return false;
        }
    }


}

