<?php

use Boctulus\Simplerest\Core\CliRouter;
use Boctulus\Simplerest\Core\Exceptions\MiddlewareNotFoundException;
use Boctulus\Simplerest\Core\Libs\Cli;
use Boctulus\Simplerest\Modules\FirebaseTest\FirebaseTest;


// Inicializar el router
$route = CliRouter::getInstance();


// funciones anonimas sin parametros --ok
CliRouter::command('version', function() {
    return 'SimpleRest Framework v1.0.0';
});

// funciones anonimas con parametros --ok
CliRouter::command('pow', function($num, $exp) {
    return pow($num, $exp);
});

CliRouter::command('test_custom_exception', function() {

    try {
        throw new MiddlewareNotFoundException();
    } catch (\Exception $e) {
        // Captura la excepción y muestra el mensaje
        var_dump($e->getMessage());  // vacio
    }
    
});

// Compilar todas las rutas CLI
CliRouter::compile();