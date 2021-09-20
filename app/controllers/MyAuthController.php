<?php

namespace simplerest\controllers;

use simplerest\core\Controller;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\libs\Factory;
use simplerest\libs\DB;
use simplerest\libs\Files;
use simplerest\traits\DbAccess;

global $api_version;

if (substr($api_version, 0, 1) != 'v' || !is_numeric(substr($api_version, 1))){
    throw new \Exception("Invalid API version");
}

/*
    Agregue según se vayan creando versiones
*/

if ($api_version == 'v1'){
    class AuthController extends \simplerest\core\api\v1\AuthController {}
}

/*
    Aquí puede usar los hooks disponibles
*/
class MyAuthController extends AuthController {
    use DbAccess;

    /*
        Hooks
    */

    function onRegister(Array $data){

    }
    
    function onRegistered($data, $uid, $is_active, $roles){ 
        global $api_version;

        $u = DB::table($this->users_table);
        $email_in_schema = $u->inSchema([$this->__email]);

        /* 
            Email confirmation
        */  
        if (!$this->config['pre_activated']){  
            $email_confirmation = $email_in_schema && $u->inSchema([$this->__confirmed_email]);

            if ($email_confirmation)
            {                 
                $exp = time() + $this->config['email_token']['expires_in'];
                $base_url =  http_protocol() . '://' . $_SERVER['HTTP_HOST'] . ($this->config['BASE_URL'] == '/' ? '/' : $this->config['BASE_URL']) ;
                $token = $this->gen_jwt_email_conf($data[$this->__email], $roles, []);
                $url = $base_url . (!$this->config['REMOVE_API_SLUG'] ? "api/$api_version" : $api_version) . '/auth/confirm_email/' . $token . '/' . $exp; 

                // Queue email
                $ok = (bool) DB::table('email_notifications')
                ->create([
                    'to_addr'   => $data[$this->__email], 
                    'to_name'    => '', 
                    'subject'    => 'Confirmación de correo', 
                    'body'       => "Para confirmar su cuenta siga el enlace:<br/><a href='$url'>$url</a>"
                ]);

                if ($this->config['debug']){
                    Files::logger("Email confirmation link for user with id = $uid is $url");
                }                
            }
        }
    }

    function onRemember($data){

    }
    
    function onRemembered($data, $link)
    {
        // Queue email
        $ok = (bool) DB::table('email_notifications')
        ->create([
            'to_addr'    => $data['email'], 
            'to_name'    => '', 
            'subject'    => 'Cambio de contraseña', 
            'body'       => "Para cambiar la contraseña siga el enlace:<br/><a href='$link'>$link</a>"
        ]);

        /*
            Posteriormente leer la tabla email_notifications y....
            basado en un tamplate, hacer algo como:

            $mail_sent = Utils::sendMail($email, null, 'Recuperación de password', "Hola!<p/>Para re-establecer la el password siga el enlace</br>$url");

            o lo mismo pero usando un Service Provider
        */

        if (!$ok && $this->config['debug']){
            Files::logger("remember-me error al agendar envio de correo a {$data[$this->__email]}");
            exit;
        }

         Files::logger("remember-me $link");
    }
    
}