#!/usr/bin/env php
<?php

use Boctulus\Simplerest\Core\CliRouter;
use Boctulus\Simplerest\Core\FrontController;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\Env;
use Boctulus\Simplerest\Core\Libs\Files;
use Boctulus\Simplerest\Core\Libs\Strings;

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


# Implementacoon de patron Command
#
# https://chatgpt.com/c/b27203dd-bc30-4950-a3c8-8a4e6ecb25d8
# https://chatgpt.com/c/2e4ac7e1-7ac7-4d86-ba9f-f1d61264504b
#

// The "routing" system is disabled after found a command to execute
$routing = true;

$args = array_slice($argv, 1);

if (count($args) > 0){
   $name         = Strings::snakeToCamel(array_shift($args));
   $commandClass = $name . "Command";

   $comm_files   = Files::glob(COMMANDS_PATH, '*Command.php');

   foreach ($comm_files as $file){
      $_name = Strings::matchOrFail(Files::convertSlashes($file, '/'), '|/([a-zA-Z0-9_]+)Command.php|');
      
      if ($name != $_name){
         continue;
      }

      require $file;

      if (class_exists($commandClass)){      
         $commandInstance = new $commandClass();
         
         if (method_exists($commandInstance, 'handle')) {
            $commandInstance->handle($args);
            $routing = false;
         } else {
            throw new \Exception("Command without handle");
         }
      }
   }
}

if ($routing){
   $cfg = Config::get();

   if ($cfg['console_router']){
      include CONFIG_PATH . 'cli_routes.php';
      CliRouter::compile();
      CliRouter::resolve();
   }

   if ($cfg['front_controller']){
      FrontController::resolve();
   }
}
     

 
 	



