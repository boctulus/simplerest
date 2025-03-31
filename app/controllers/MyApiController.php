<?php

namespace Boctulus\Simplerest\Controllers;

use Boctulus\Simplerest\Core\Interfaces\IApi;
use Boctulus\Simplerest\Core\Libs\Strings;

use Boctulus\Simplerest\Core\Traits\SubResourcesV1;
use Boctulus\Simplerest\Core\Traits\SubResourceHandler;
use Boctulus\Simplerest\traits\SubResourcesV3;

global $api_version;

$api_version = (string) $api_version;

if (!isset($api_version[0]) || $api_version[0] != 'v' || !is_numeric(substr($api_version, 1))){
    throw new \Exception("Invalid API version. Expected something like 'v1'. Found: '$api_version'");
}

/*
    Agregue según se vayan creando versiones
*/

if ($api_version == 'v1'){
    class MyApiController extends \Boctulus\Simplerest\Core\api\v1\ApiController {
        use SubResourceHandler;
    }
    return;
}



