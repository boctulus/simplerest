<?php

use Boctulus\Simplerest\middlewares\InyectarSaludo;
use Boctulus\Simplerest\middlewares\InyectarInfoEmpresa;
use Boctulus\Simplerest\middlewares\InyectarUsername;

/*
    Middleware registration
*/

return [
    // 'Boctulus\Simplerest\Controllers\MyAuthController@login' => InyectarUsername::class,
    'Boctulus\Simplerest\Controllers\TestController@mid' => InyectarSaludo::class,
    // 'Boctulus\Simplerest\Controllers\TestController' => InyectarSaludo::class,
    // 'Boctulus\Simplerest\Controllers\MyAuthController@login' =>  InyectarInfoEmpresa::class
];