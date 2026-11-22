<?php

namespace Boctulus\Simplerest\Controllers;

use Boctulus\Simplerest\Core\Api\AuthController;

/*
    Application authentication controller. The core resolves it through
    Factory::auth() for API requests.

    AuthController is provided by boctulus/simplerest-jwt-auth:
        composer require boctulus/simplerest-jwt-auth

    Override the hooks below to add application behaviour.
*/
class MyAuthController extends AuthController {

    function onRegister(Array $data){ }
    function onRegistered(Array $data, $uid, $is_active, $roles){ }
    function onRemember(Array $data){ }
    function onRemembered(Array $data, $link_url){ }
    function onLogin(Array $data){ }
    function onLogged(Array $data, $uid, $is_active, $roles, $perms){ }
    function onImpersonated(Array $data, $uid, $is_active, $roles, $perms, $impersonated_by){ }
    function onChecked($uid, $is_active, $roles, $perms, $auth_method){ }
    function onConfirmedEmail($uid, $roles, $perms){ }
    function onChangedPassword($uid, $roles, $perms){ }

    function getDbAccess($uid) : Array { return []; }
}
