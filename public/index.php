<?php

	require_once __DIR__ . '/app.php';
    
    use simplerest\core\Route;
    use simplerest\core\FrontController;
    

    if ($config['ROUTER']){        
        include __DIR__ . '../../config/routes.php';
        Route::compile();
        Route::resolve();
    } 

    if ($config['FRONT_CONTROLLER']){        
        FrontController::resolve();
    } 

    //throw new Exception('There is no Router or FrontController enabled!');

	



