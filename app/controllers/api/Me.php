<?php

namespace simplerest\controllers\api;

global $api_version;

/*
    Agregue según se vayan creando versiones
*/

if ($api_version == 'v1'){
    class Me extends \simplerest\core\api\v1\MySelf { }
    return;
}

