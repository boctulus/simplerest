<?php

	require_once __DIR__ . '/app.php';
    
    use simplerest\core\Route;
    use simplerest\core\FrontController;
    

    if (config()['router']){        
        include __DIR__ . '../../config/routes.php';
        Route::compile();
        Route::resolve();
    } 

    if (config()['front_controller']){        
        FrontController::resolve();
    } 

    //throw new Exception('There is no Router or FrontController enabled!');

	



