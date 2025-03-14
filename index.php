<?php

/*
    Framework Name: Simplerest
    Description: API Rest framework for PHP
    Author: Pablo Bozzolo
    Author Email: boctulus@gmail.com
*/

require_once __DIR__ . '/app.php';

use simplerest\core\libs\Config;
use simplerest\core\CliRouter;
use simplerest\core\WebRouter;
use simplerest\core\FrontController;

$cfg = Config::get();

if ($cfg['web_router']){        
    include __DIR__ . '/config/routes.php';
    WebRouter::compile();
    WebRouter::resolve();
} 

if ($cfg['console_router']){        
    CliRouter::resolve();
} 

if ($cfg['front_controller']){        
    FrontController::resolve();
} 

//throw new Exception('There is no Router or FrontController enabled!');

