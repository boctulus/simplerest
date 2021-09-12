<?php 

namespace simplerest\libs;

use PHPMailer\PHPMailer\PHPMailer;
use simplerest\libs\Factory;

class Utils {

    /*
        Gmail => habilitar:

        https://myaccount.google.com/lesssecureapps
    */
    static function sendMail(string $to_email, string $to_name, $subject, $body, $alt_body = null){
		$config = Factory::config();

		$mail = new PHPMailer();
        $mail->isSMTP();

        $mailer = $config['email']['mailer_default'];
        
        foreach ($config['email']['mailers'][$mailer] as $k => $prop){
			$mail->{$k} = $prop;
        }	
    
        $mail->setFrom(
            $config['email']['from']['address'], 
            $config['email']['from']['name']
        );

        $mail->addAddress($to_email, $to_name);
        $mail->Subject = $subject;
		$mail->msgHTML($body); 
		
		if (!is_null($alt_body))
			$mail->AltBody = $alt_body;
		
		//$mail->addAttachment('images/phpmailer_mini.png');       
        

        if (!$mail->send())
        {	
            return $mail->ErrorInfo;
        }else
            return true;
	}
}