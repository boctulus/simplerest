<?php

namespace simplerest\controllers;

use simplerest\core\ConsoleController;
use simplerest\libs\Hardware;
use simplerest\libs\StdOut;
use simplerest\libs\Files;
use simplerest\libs\Strings;
use simplerest\libs\Schema;
use simplerest\libs\DB;

/*
    Update commands
*/

class UpdateController extends ConsoleController
{
    static public $update_path;
    
    function __construct()
    {
        $dirs = [];
        foreach (new \DirectoryIterator(UPDATE_PATH) as $fileInfo) {
            if($fileInfo->isDot() || !$fileInfo->isDir()) continue;
            
            $dirs[] = $fileInfo->getBasename();
        }

        /*
            La forma de ordenamiento no es del todo correcta !

            1.0.1-beta < 1.0.11

            porque ...

            1.0.1-beta = 1.0.1.1 
        */
        sort($dirs);

        $last_ver_dir = end($dirs);
        static::$update_path = ROOT_PATH . 'updates/'. $last_ver_dir . '/';
    }

    /*
        Verificar NO esté corriendo en mi PC para evitar un desastre
    */
    protected function check(){
        $id = Hardware::UniqueMachineID();

        if (ROOT_PATH == '/home/www/simplerest/' && $id == 'd57b457667c91f55e9dee697950e5d04'){
            StdOut::pprint("Running at Home. Aborting (..)");
            exit;
        }
    }     

    // protected
    function run_batches(){
        /*
            Debe existir *persistencia* en algún lado 

            - Deben correr como las migraciones,... PERO....
            - La base de datos tiene que estar CONTENIDA en el código fuente => 

            Crear un archivo de texto por cada batch ejecutado (emulando registros en 'migrations')
        */

        $update_path = static::$update_path . 'batches/';

        Files::mkDir(static::$update_path . 'completed/');

        $files = glob($update_path . '*.php');
        foreach ($files as $file){
            $completed_path = static::$update_path . 'completed/' . basename($file);

            if (file_exists($completed_path)){
                continue;
            }

            $class_name = Strings::getClassNameByFileName($file);

            require_once $file;

            if (!class_exists($class_name)){
                throw new \Exception ("Class '$class_name' doesn't exist in $file");
            } 

            StdOut::pprint("~~~ Executing batch $file", true);

            $update = new $class_name();
            $ok = $update->run();

            if (!$ok){
                StdOut::pprint("Batch $file exited with error", true);
                exit;
            }

            file_put_contents($completed_path, "Completed at ".at());
        }
    }

    function install(...$opt){
        $this->check();
        
        /*
            Copy files 
        */

        $ori =  static::$update_path . 'files';
        $dst = ROOT_PATH;

        $except =  [
            'db_dynamic_load.php',
            'docs/dev',
            'glob:*.zip'
        ];

        Files::copy($ori, $dst, null, $except);

        /*
            Run batches
        */

        $this->run_batches();
    }

    function rollback(...$opt){
        $this->check();

        // ...
    }


    function description(...$opt){

    }

    function version(...$opt){
        
    }

    function is_updated(){
        
    }

   
}

