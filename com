#!/usr/bin/env php
<?php

use simplerest\core\FrontController;
use simplerest\core\libs\Config;
use simplerest\core\libs\DB;
use simplerest\core\libs\Env;


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


# Implementar patron Command
#
# https://chatgpt.com/c/b27203dd-bc30-4950-a3c8-8a4e6ecb25d8
#

/*
   Command mysql_log

   php com mysql_log on                                  DB::dbLogOn()
   php com mysql_log off                                 DB::dbLogOff()
   php com mysql_log start [-filename=]  que ejecuta ..  DB::dbLogStart()       
   php com mysql_log dump                                DB::dbLogDump()   
*/

function mysql_log($args)
{
   $fst = array_shift($args);

   if ($fst == 'on'){
      dd("Iniciando logs ...");
      DB::dbLogOn();
      return;
   }

   if ($fst == 'off'){
      dd("Desactivando logs ...");
      DB::dbLogOff();
      return;
   }

   if ($fst == 'start'){
      dd("Activando logs ...");
      DB::dbLogStart();
      return;
   }

   if ($fst == 'dump'){
      dd("Volcando logs ...");
      DB::dbLogDump();
      return;
   }         
}

/*
   Command help
*/
function help($args){
   $str = <<<STR
   php com mysql_log on                                  DB::dbLogOn()
   php com mysql_log off                                 DB::dbLogOff()
   php com mysql_log start [-filename=]  que ejecuta ..  DB::dbLogStart()       
   php com mysql_log dump                                DB::dbLogDump() 
   STR;

   dd($str);
}


$routing = true;

$args = array_slice($argv, 1);

if (count($args) > 0){
   $method_name = array_shift($args);

   // Aca habria un foreach y si coincide con el nombre de una clase de tipo Command invocaria el handle()

   if ($method_name == 'mysql_log'){
      mysql_log($args);
      $routing = false;
   }

   if ($method_name == 'help'){
      help($args);
      $routing = false;
   }
}



if ($routing){
   FrontController::resolve();
}
 	



