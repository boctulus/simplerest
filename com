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
parse_str(implode('&', array_slice($_SERVER['argv'], 3)), $_GET);

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


# Implementación de patrón Command
# Se hace autodiscovery de comandos en app/Commands y packages/*/*/src/Commands
#
$routing = true;

$args = array_slice($argv, 1);

if (count($args) > 0){
   $name         = Strings::snakeToCamel(array_shift($args));
   $commandClass = $name . "Command";

   // Mantengo compatibilidad con la forma tradicional (namespace desde Config), pero
   // el discovery permitirá comandos en packages sin necesidad de modificar autoload.
   $namespace = Config::get()['namespace'] ?? null;
   $commandClassWithNamespace = $namespace ? ($namespace . "\\Commands\\" . $commandClass) : null;

   //
   // --- AUTODISCOVERY DE COMMANDS (app + packages) ---
   //
   // (Podria usar el helper package.php que provee funciones de autodiscovery)

   $searchPaths = [];

   // Ruta histórica de comandos en app (constante COMMANDS_PATH debe existir)
   if (defined('COMMANDS_PATH')) {
       $searchPaths[] = COMMANDS_PATH;
   } else {
       // fallback sensible: app/Commands relativo a la app.php
       $appCommands = realpath(__DIR__ . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Commands');
       if ($appCommands && is_dir($appCommands)) {
           $searchPaths[] = $appCommands;
       }
   }

   $packagesBase = realpath(__DIR__ . DIRECTORY_SEPARATOR . 'packages');

   if ($packagesBase && is_dir($packagesBase)) {
       // primer nivel: autores (p.ej. "boctulus")
       $authorDirs = glob($packagesBase . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);
       if ($authorDirs !== false) {
           foreach ($authorDirs as $authorDir) {
               // segundo nivel: paquetes dentro del author (p.ej. "zippy")
               $pkgDirs = glob($authorDir . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);
               if ($pkgDirs === false) continue;

               foreach ($pkgDirs as $pkgDir) {
                   // paths posibles donde pueden existir comandos
                   $c1 = $pkgDir . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Commands';
                   $c2 = $pkgDir . DIRECTORY_SEPARATOR . 'Commands';

                   if (is_dir($c1)) {
                       $searchPaths[] = $c1;
                   }
                   if (is_dir($c2)) {
                       $searchPaths[] = $c2;
                   }
               }
           }
       }
   }

   // Normalizo rutas y obtengo todos los archivos *Command.php de esas rutas (evito duplicados)
   $comm_files = [];
   foreach ($searchPaths as $p) {
       // Evito paths vacíos o no existentes
       if (!$p || !is_dir($p)) continue;
       $files = Files::glob($p, '*Command.php');
       if ($files && is_array($files)) {
           foreach ($files as $f) {
               $rp = realpath($f);
               if ($rp) $comm_files[$rp] = $f;
               else $comm_files[$f] = $f;
           }
       }
   }

   // dd($comm_files);

   // Recorro archivos y busco el que coincida con el nombre solicitado
   foreach ($comm_files as $file) {
      // Intento extraer el nombre base por convención: /SomeNameCommand.php
      $match = Strings::matchOrFail(Files::convertSlashes($file, '/'), '|/([a-zA-Z0-9_]+)Command.php|');
      $_name = $match;

      if ($name != $_name){
         continue;
      }

      // Antes de require: obtengo clases declaradas
      $before = get_declared_classes();

      // Requiero el archivo (si tiene namespace la clase quedará registrada)
      try {
          require_once $file;
      } catch (\Throwable $e) {
          // Si el require falla informo con detalle (pero dejo que el flujo principal pueda intentar otras rutas)
          fwrite(STDERR, "Error cargando comando desde {$file}: " . $e->getMessage() . PHP_EOL);
          continue;
      }

      // Después de require: nuevas clases
      $after = get_declared_classes();
      $diff = array_values(array_diff($after, $before));

      // Busco en las clases nuevas una que termine en "Command"
      $foundClass = null;
      foreach ($diff as $class) {
          if (preg_match('/Command$/', $class)) {
              // Si queremos, verificamos que la clase contenga el nombre base (e.g. Zippy in ZippyCommand)
              if (stripos($class, $_name) !== false || true) {
                  $foundClass = $class;
                  break;
              }
          }
      }

      // Si no hallamos clase nueva (ej. ya estaba cargada por autoload), comprobamos clases ya declaradas
      if (!$foundClass) {
          foreach (get_declared_classes() as $class) {
              if (preg_match('/\\\?' . preg_quote($_name . 'Command', '/') . '$/i', $class)) {
                  $foundClass = $class;
                  break;
              }
          }
      }

      // Si aún no se encontró, intentamos la clase según namespace configurado (compatibilidad retro)
      if (!$foundClass && $commandClassWithNamespace && class_exists($commandClassWithNamespace)) {
          $foundClass = $commandClassWithNamespace;
      }

      if ($foundClass && class_exists($foundClass)) {
          // Crear instancia y ejecutar si tiene handle
          try {
              $commandInstance = new $foundClass();
          } catch (\Throwable $e) {
              throw new \Exception("No se pudo instanciar el comando {$foundClass}: " . $e->getMessage());
          }

          if (method_exists($commandInstance, 'handle')) {
              $commandInstance->handle($args);
              $routing = false;
              break; // ya ejecuté el comando; salgo
          } else {
              throw new \Exception("Command {$foundClass} encontrado pero no define handle()");
          }
      }
   } // foreach comm_files
} // if count(args) > 0

if ($routing){
   $cfg = Config::get();

   $handled = false;

   if (!empty($cfg['console_router'])){
      include CONFIG_PATH . 'cli_routes.php';
      CliRouter::compile();
      $handled = CliRouter::resolve();
   }

   if (!$handled && !empty($cfg['front_controller'])){
      FrontController::resolve();
   }
}
