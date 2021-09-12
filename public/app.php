<?php

    // App bootstraping

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require __DIR__.'/../vendor/autoload.php';

    $config = include __DIR__ . '/../config/config.php';

    $dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
    $dotenv->load();


    /* prevent XSS. */
    $_GET   = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
    $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);


    foreach (new \DirectoryIterator(HELPERS_PATH) as $fileInfo) {
        if($fileInfo->isDot()) continue;
        
        $path = $fileInfo->getPathName();

        if(pathinfo($path, PATHINFO_EXTENSION) == 'php'){
            require_once $path;
        }
    }   

    foreach ($config['providers'] as $provider){
        $p = new $provider();
        $p->boot();
    }
    