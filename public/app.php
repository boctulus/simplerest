<?php

use simplerest\core\libs\i18n\Translate;

// App bootstraping

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once  __DIR__ . '/../config/constants.php';

ini_set("log_errors", 1);
ini_set('error_log', LOGS_PATH . 'errors.txt');

if (!file_exists(ROOT_PATH .'composer.json')){
    throw new \Exception("File composer.json is missing");
}       

if (!file_exists(ROOT_PATH . 'vendor'. DIRECTORY_SEPARATOR .'autoload.php')){
    chdir(__DIR__);
    exec("composer install --no-interaction");
    sleep(10);
}

require_once __DIR__.'/../vendor/autoload.php';

if (class_exists('Dotenv\\Dotenv')){
    $class  = Dotenv\Dotenv::class;
    $dotenv = $class::createImmutable(__DIR__ . '/..');
    $dotenv->load();
}

/*
    El nivel de reporte de errores debería depender de si el modo es DEBUG o no
*/

/* 
    Prevent XSS.

    https://benhoyt.com/writings/dont-sanitize-do-escape/
    https://stackoverflow.com/questions/69207368/constant-filter-sanitize-string-is-deprecated    
*/
$_GET   = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

/* Helpers */
$helper_dirs = [__DIR__ . '/../app/core/helpers', __DIR__ . '/../app/helpers'];

foreach ($helper_dirs as $dir){
    foreach (new \DirectoryIterator($dir) as $fileInfo) {
        if($fileInfo->isDot()) continue;
        
        $path = $fileInfo->getPathName();

        if(pathinfo($path, PATHINFO_EXTENSION) == 'php'){
            require_once $path;
        }
    }    
}
    
$config = include __DIR__ . '/../config/config.php';
        
/*
    i18n
*/

Translate::useGettext(config()['translate']['use_gettext']);

$req  = request(); 
$lang = $req->shiftQuery('lang') ?? $req->header('Accept-Language');
setLang($lang); 

foreach ($config['providers'] as $provider){
    $p = new $provider();
    $p->register();
    $p->boot();
}

/*
    TimeZone adjust

    Lo ideal seria que esto este dentro de un package y que se pueda desconectar
*/

$cfg = config();
if (isset($cfg['DateTimeZone'])){
    $ok = date_default_timezone_set($cfg['DateTimeZone']);
    
    if (!$ok){
        dd("FALLO AL INTENTAR CAMBIAR TIMEZONE");
    }
}