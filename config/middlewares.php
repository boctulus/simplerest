<?php

use simplerest\middlewares\InyectarSaludo;
use simplerest\middlewares\InyectarInfoEmpresa;

/*
    Middleware registration
*/

return [
    'simplerest\controllers\TestController@mid' => InyectarSaludo::class,
    'simplerest\controllers\TestController' => InyectarSaludo::class,
    'simplerest\controllers\MyAuthController@login' =>  InyectarInfoEmpresa::class
];