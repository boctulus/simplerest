<?php

/*
    Framework Name: Simplerest
    Description: API Rest framework for PHP
    Author: Pablo Bozzolo
    Author Email: boctulus@gmail.com
*/

require_once __DIR__ . '/app.php';

use simplerest\core\Route;
use simplerest\core\FrontController;

if (config()['router']){        
    include __DIR__ . '/config/routes.php';
    Route::compile();
    Route::resolve();
} 

if (config()['front_controller']){        
    FrontController::resolve();
} 

//throw new Exception('There is no Router or FrontController enabled!');

