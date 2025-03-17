<?php

use simplerest\core\CliRouter;
use simplerest\core\libs\System;
use simplerest\core\libs\Logger;

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

// Comandos con controladores
CliRouter::command('dbdriver', 'simplerest\controllers\DumbController@db_driver');

// Comandos con controladores -- en este caso se pasan parametros sin validacion
CliRouter::command('plus_1', 'simplerest\controllers\DumbController@inc2');

// mas... aun sin soporte

// // Comandos con controladores -- en este caso se pasan parametros con validacion
// CliRouter::command('increment/{num}', 'simplerest\controllers\folder\SomeController@inc2')
// ->where(['num' => '[0-9]+']);

// // Comandos con parámetros y restricciones
// CliRouter::command('migrations {action}', 'MigrationsController@handle')
//     ->where(['action' => 'migrate|rollback|status|reset']);

// // Comandos con nombres descriptivos
// CliRouter::command('route:list', 'RouteController@listRoutes')
//     ->name('routes.list');

// //Comandos con alias
// CliRouter::command('serve', function() {
//     $port = 8000;
//     $host = 'localhost';
//     System::execAtRoot("php -S $host:$port -t public");
//     return "Server started at http://$host:$port";
// })->alias('server');

// // Comandos con subcomandos agrupados
// CliRouter::group('db', function() {
//     CliRouter::command('backup', 'DatabaseController@backup');
//     CliRouter::command('restore {file}', 'DatabaseController@restore')
//         ->where(['file' => '.+\.sql']);
//     CliRouter::command('optimize', 'DatabaseController@optimize');
// });

// // Comandos con argumentos variables
// CliRouter::command('test {file?}', function($file = null) {
//     if ($file) {
//         return System::execAtRoot("phpunit $file");
//     }
//     return System::execAtRoot("phpunit");
// });

// // Comandos con opciones
// CliRouter::command('env:set {key} {value}', function($key, $value) {
//     // Actualizar el archivo .env
//     $envFile = ROOT_PATH . '.env';
//     $content = file_get_contents($envFile);
//     $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
//     file_put_contents($envFile, $content);
//     return "Environment variable {$key} set to {$value}";
// });

// Compilar todas las rutas CLI
CliRouter::compile();