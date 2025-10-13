<?php

namespace Boctulus\Simplerest\Controllers\api;

global $api_version;

/*
    Agregue según se vayan creando versiones
*/

if ($api_version == 'v1'){
    class Files extends \Boctulus\Simplerest\Core\API\v1\Files { }
    return;
};

