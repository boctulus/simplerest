<?php

namespace Boctulus\Simplerest\Controllers\api;

global $api_version;

/*
    Agregue según se vayan creando versiones
*/

if ($api_version == 'v1'){
    class Me extends \Boctulus\Simplerest\Core\api\v1\MySelf { }
    return;
}

