<?php

use simplerest\middlewares\InyectarSaludo;
use simplerest\middlewares\InyectarInfoEmpresa;
use simplerest\middlewares\InyectarUsername;

/*
    Middleware registration
*/

return [
    'simplerest\controllers\MyAuthController@login' => InyectarUsername::class,
    // 'simplerest\controllers\TestController@mid' => InyectarSaludo::class,
    // 'simplerest\controllers\TestController' => InyectarSaludo::class,
    // 'simplerest\controllers\MyAuthController@login' =>  InyectarInfoEmpresa::class
];