<?php

namespace Boctulus\Simplerest\Controllers\Api;

global $api_version;

/*
    Agregue según se vayan creando versiones
*/

if ($api_version == 'v1'){
    class Updates extends \Boctulus\Simplerest\Core\Api\Files { 
        public $model_name = 'UpdatesModel';

    }

    return;
};

