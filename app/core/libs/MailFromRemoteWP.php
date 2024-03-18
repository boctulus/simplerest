<?php declare(strict_types=1);

namespace simplerest\core\libs;

use simplerest\core\libs\Logger;
use simplerest\core\interfaces\IMail;

/*  
    Ej:
    
    MailFromRemoteWP::setRemote($url); // <-- url al endpoint
    
    $res = MailFromRemoteWP::send($email, $subject, $content);
*/
class MailFromRemoteWP extends MailBase
{
    protected static $url;
    
    static function setRemote($url){
        static::$url = $url;
    }

    static function send($to, $subject = '', $body = '', $attachments = null, $from = [], Array $cc = [], $bcc = [], $reply_to = [], $alt_body = null){
		if (empty(static::$url)){
            throw new \Exception("Set remote WP url first");
        }

        $body = trim($body); 

        if (empty($subject)){
            throw new \Exception("Subject is required");
        }

        if (empty($body) && empty($alt_body)){
            throw new \Exception("Body or alt_body is required");
        }

        if (!is_array($to)){
            $tmp = $to;
            $to  = [];
            $to[]['email'] = $tmp;
        } else {
            if (Arrays::isAssocc($to)){
                $to = [ $to ];
            }
        }

        if (!is_array($cc)){
            $tmp = $cc;
            $cc  = [];
            $cc[]['email'] = $tmp;
        } else {
            if (Arrays::isAssocc($cc)){
                $cc = [ $cc ];
            }
        }

        if (!is_array($bcc)){
            $tmp = $bcc;
            $bcc  = [];
            $bcc[]['email'] = $tmp;
        } else {
            if (Arrays::isAssocc($bcc)){
                $bcc = [ $bcc ];
            }
        }

        if (!is_array($from)){
            $tmp = $from;
            $from = [];
            $from['email'] = $tmp;
        } 

        $data = [
            'to'      => $to[0]['email'],
            'subject' => $subject,
            'body'    => $body
        ];
        
        $res = consume_api(static::$url, 'POST', $data, [
            'User-Agent' => 'PostmanRuntime/7.34.0'
        ]);
        
        return $res;
	}
    
}