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

// mas...


// // Comandos con parámetros y restricciones
// CliRouter::command('migrations {action}', 'MigrationsController@handle')
//     ->where(['action' => 'migrate|rollback|status|reset']);

// // Comandos con controladores
// CliRouter::command('make:controller {name}', 'GeneratorController@makeController')
//     ->where(['name' => '[A-Za-z0-9_]+']);

// CliRouter::command('make:model {name}', 'GeneratorController@makeModel')
//     ->where(['name' => '[A-Za-z0-9_]+']);

// // Comandos con funciones anónimas
// CliRouter::command('git:pull', function() {
//     return System::execAtRoot("git pull");
// });

// CliRouter::command('cache:clear', function() {
//     $cacheDir = STORAGE_PATH . 'cache';
//     System::execAtRoot("rm -rf $cacheDir/*");
//     return "Cache cleared successfully.";
// });

// // Comandos con nombres descriptivos
// CliRouter::command('route:list', 'RouteController@listRoutes')
//     ->name('routes.list');

// // Comandos con alias
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

// // Comandos para tareas específicas del sistema
// CliRouter::command('logs:clear', function() {
//     System::execAtRoot("rm -f " . STORAGE_PATH . "logs/*.log");
//     return "Logs cleared successfully.";
// });

// // Comandos utilitarios
// CliRouter::command('get_path_public', function() {
//     return PUBLIC_PATH;
// });


// Compilar todas las rutas CLI
CliRouter::compile();