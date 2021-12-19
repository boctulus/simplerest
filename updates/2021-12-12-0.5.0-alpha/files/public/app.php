<?php

    // App bootstraping

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require __DIR__.'/../vendor/autoload.php';

    if (class_exists('Dotenv\\Dotenv')){
        $class  = Dotenv\Dotenv::class;
        $dotenv = $class::createImmutable(__DIR__ . '/..');
        $dotenv->load();
    }

    /* Prevent XSS. */
    $_GET   = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
    $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

    /* Helpers */
    foreach (new \DirectoryIterator(__DIR__ . '/../app/helpers') as $fileInfo) {
        if($fileInfo->isDot()) continue;
        
        $path = $fileInfo->getPathName();

        if(pathinfo($path, PATHINFO_EXTENSION) == 'php'){
            require_once $path;
        }
    }
    
    $config = include __DIR__ . '/../config/config.php';
            
    // i18n
    $req  = request(); 
    $lang = $req->shiftQuery('lang') ?? $req->header('Accept-Language');
    setLang($lang); 

    foreach ($config['providers'] as $provider){
        $p = new $provider();
        $p->boot();
    }
    