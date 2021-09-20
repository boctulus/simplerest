<?php

namespace simplerest\controllers;

use simplerest\core\interfaces\IApi;
use simplerest\libs\Strings;

global $api_version;

if ($api_version[0] != 'v' || !is_numeric(substr($api_version, 1))){
    throw new \Exception("Invalid API version");
}

/*
    Agregue según se vayan creando versiones
*/

if ($api_version == 'v1'){
    class MyApiController extends \simplerest\core\api\v1\ApiController {}
    return;
}



