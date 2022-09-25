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
    function onRegister(Array $data){ }
    function onRegistered(Array $data, $uid, $is_active, $roles){ }
    function onRemember(Array $data){}

    /*
        Ejemplo
    */
    function onRemembered(Array $data, $link_url){
        // Envio el correo aca

        response([
            'message' => 'Correo enviado'
        ]);
    }
    
    function onLogin(Array $data){}
    function onLogged(Array $data, $uid, $is_active, $roles, $perms){}
    function onImpersonated(Array $data, $uid, $is_active, $roles, $perms, $impersonated_by){}	
    function onChecked($uid, $is_active, $roles, $perms, $auth_method){}
    function onConfirmedEmail($uid, $roles, $perms){}
    function onChangedPassword($uid, $roles, $perms){}

    function getDbAccess($uid) : Array { return []; }

}

