<?php

use simplerest\core\libs\i18n\Translate;
use simplerest\core\libs\Config;

// App bootstraping

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once __DIR__ . '/scripts/init/redirection/redirection.php';

require_once __DIR__ . '/config/constants.php';

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

require_once __DIR__.'/vendor/autoload.php';

if (class_exists('Dotenv\\Dotenv')){
    $class  = Dotenv\Dotenv::class;
    $dotenv = $class::createImmutable(__DIR__);
    $dotenv->load();
}

/* 
    Prevent XSS.

    https://benhoyt.com/writings/dont-sanitize-do-escape/
    https://stackoverflow.com/questions/69207368/constant-filter-sanitize-string-is-deprecated    
*/
$_GET   = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);


/* Helpers */

$autoload = include __DIR__ . '/config/autoload.php';

$includes  = $autoload['include']; 
$excluded  = $autoload['exclude'];     

foreach ($includes as $file_entry){
    if (!is_dir($file_entry)){
        if(pathinfo($file_entry, PATHINFO_EXTENSION) == 'php'){
            require_once $file_entry;
            continue;
        }
    }

    foreach (new \DirectoryIterator($file_entry) as $fileInfo) {
        if($fileInfo->isDot()) continue;
        
        $path     = $fileInfo->getPathName();
        $filename = $fileInfo->getFilename();

        // No incluyo archivos que comiencen con "_"
        if (substr($filename, 0, 1) == '_'){            
            continue;
        }

        if (in_array($path, $excluded)){
            continue;
        }

        if(pathinfo($path, PATHINFO_EXTENSION) == 'php'){
            require_once $path;
        }
    }    
}
    
$config = include __DIR__ . '/config/config.php';
        
/*
    i18n
*/

Translate::useGettext($config['translate']['use_gettext']);

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

if (isset($config['DateTimeZone'])){
    $ok = date_default_timezone_set($config['DateTimeZone']);
    
    if (!$ok && $config['debug']){
        dd("Error trying to change TimeZone");
    }
}

// Mostrar errores
if ((php_sapi_name() === 'cli') || (isset($_GET['show_errors']) && $_GET['show_errors'] == 1)){
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
} else {
	if ($config['debug'] == false){
		error_reporting(E_ALL & ~E_WARNING);
		error_reporting(0);
	}	
}