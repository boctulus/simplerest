<?php

namespace simplerest\controllers;

use simplerest\core\controllers\Controller;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;
use simplerest\core\libs\Files;
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
        Uso de hooks
    */

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
                $token = $this->gen_jwt_email_conf($data[$this->__email], $roles, [], $uid);
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

        
        if (in_array('superadmin', $roles)){
            return;
        }

        /*
            Registro el usuario como futuro propierario de una base de datos

            Llamo a los SP(s)
        */

        $at = datetime();

        $tenant = null;
        if (in_array('admin', $roles)){
            $tenant = 'db_' . $uid;

            $db_id = DB::table('tbl_base_datos')
            ->fill(['usu_intIdActualizador'])
            ->create([
                'dba_varNombre'    => $tenant,
                'usu_intIdCreador' => $uid,
                'usu_intIdActualizador' => $uid,
                'dba_dtimFechaCreacion' => $at,
                'dba_dtimFechaActualizacion' => $at
            ]); 

            // **
        }

        /*
            SINO creé la DB antes => la debeo crear la DB, colgado
            del evento onCreated() en TblBaseDatos

            Podría crear una tarea programada para esto que revise
            si hay pendiente crear alguna DB en 'tbl_base_datos'
        */

        if ($tenant === null){
            $tenant = request()->getTenantId();

            if ($tenant === null){
                response()->sendError("Undefined tenant", 500);
            }
        }
      
        DB::setConnection($tenant);
        //dd(DB::getCurrentConnectionId(), 'CONN ID');

        $idtbu = DB::table('tbl_usuario')
        ->fill(['usu_bolEstadoUsuario'])
        ->create(
            [
                'usu_intId' => $uid,
                'usu_varNroIdentificacion' => $data['use_varUsuario'], // ???
                'usu_varEmail' => $data['use_varEmail'],
                //'usu_varPassword' => $data['use_decPassword'],
                'usu_bolEstadoUsuario' => $data['est_intIdEstado']
                // ..
            ]
        );


        DB::getDefaultConnection();

        //
        //    Debo macthear con las conexiones de la DB
        //
        $db_id = DB::table('tbl_base_datos')
        ->where(['dba_varNombre' => $tenant])
        ->value('dba_intId');
        
        if ($db_id === null){
            throw new \Exception("Invalid database selection");
        }        

        
        $dbuid = DB::table('tbl_usuarios_x_base_datos')
        ->create([
            'bas_intIdBasedatos' => $db_id,
            'usu_intIdUsuario'   => $uid
        ]);    
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

        if ($this->config['debug']){
            if (!$ok){
                Files::logger("remember-me error al agendar envio de correo a {$data[$this->__email]}");
                exit;
            }

            Files::logger("remember-me $link");
        }    
        
        Factory::response()->send(['msg' => 'Por favor ahora revise su correo'], 200);
    }
    
}
