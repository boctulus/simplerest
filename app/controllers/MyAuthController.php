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
}

