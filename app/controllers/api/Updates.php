<?php

namespace simplerest\controllers\api;

global $api_version;

/*
    Agregue según se vayan creando versiones
*/

if ($api_version == 'v1'){
    class Updates extends \simplerest\core\api\v1\Files { 
        public $model_name = 'UpdatesModel';

    }

    return;
};

