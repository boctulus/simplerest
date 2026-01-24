<?php

namespace Boctulus\Simplerest\Controllers;

use Boctulus\Simplerest\Core\Api\v1\AuthController;
use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\Files;
use Boctulus\Simplerest\Core\Request;
use Boctulus\Simplerest\Core\Response;
use Boctulus\Simplerest\traits\DbAccess;

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

