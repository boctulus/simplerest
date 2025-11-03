<?php

use Boctulus\FriendlyposWeb\Models\UnidadMedidaModel;
use Boctulus\Simplerest\Core\CliRouter;
use Boctulus\Simplerest\Core\Exceptions\MiddlewareNotFoundException;
use Boctulus\Simplerest\Core\Libs\Cli;
use Boctulus\Simplerest\Core\Libs\DB;
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

/*
   Pseudo-ORM
*/

CliRouter::command('db:test_orm', function() {
    DB::getConnection('laravel_pos');

    set_model_namespace('laravel_pos', 'Boctulus\FriendlyposWeb');

    $um = UnidadMedidaModel::findOrFail(5);
    dd($um->getOne()); 

    $um = UnidadMedidaModel::findOrFail(3);
    dd($um->getOne()); 


    DB::closeConnection('laravel_pos');
});


CliRouter::command('db:test_model_access', function() {
    DB::getConnection('laravel_pos');

    set_model_namespace('laravel_pos', 'Boctulus\FriendlyposWeb');

    $res = dd(
        DB::table('unidad_medida')->get()
    );

    dd(
        DB::getLog()
    );

    DB::closeConnection('laravel_pos');

    return $res;
});

// Compilar todas las rutas CLI
CliRouter::compile();