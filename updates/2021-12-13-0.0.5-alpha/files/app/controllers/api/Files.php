<?php

namespace simplerest\controllers\api;

global $api_version;

/*
    Agregue según se vayan creando versiones
*/

if ($api_version == 'v1'){
    class Files extends \simplerest\core\api\v1\Files { }
    return;
};

