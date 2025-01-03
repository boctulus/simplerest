<?php

namespace simplerest\controllers;

use simplerest\core\interfaces\IApi;
use simplerest\core\libs\Strings;

use simplerest\core\traits\SubResourcesV1;
use simplerest\core\traits\SubResourcesV2;
use simplerest\traits\SubResourcesV3;

global $api_version;

$api_version = (string) $api_version;

if (!isset($api_version[0]) || $api_version[0] != 'v' || !is_numeric(substr($api_version, 1))){
    throw new \Exception("Invalid API version. Expected something like 'v1'. Found: '$api_version'");
}

/*
    Agregue según se vayan creando versiones
*/

if ($api_version == 'v1'){
    class MyApiController extends \simplerest\core\api\v1\ApiController {
        use SubResourcesV2;
    }
    return;
}



