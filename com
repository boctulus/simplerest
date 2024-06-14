#!/usr/bin/env php
<?php

use simplerest\core\libs\Env;
use simplerest\core\libs\Config;
use simplerest\core\FrontController;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'app.php';

/*
   Parse command line arguments into the $_GET variable <sep16@psu.edu>
*/

parse_str(implode('&', array_slice($argv, 3)), $_GET);

/*
   Procesamiento de env: y cfg:

   Ej:

   php com my_controller my_action env:variable=valor cfg:my_config_variable=3
*/

foreach ($_GET as $var => $val)
{
   $pos = strpos($var, 'env:');
   
   if ($pos === 0){
      $var = substr($var, 4);

      Env::set($var, $val);
   }

   $pos = strpos($var, 'cfg:');
   
   if ($pos === 0){
      $var = substr($var, 4);

      Config::set($var, $val);
   }
}
   
FrontController::resolve();
 	



