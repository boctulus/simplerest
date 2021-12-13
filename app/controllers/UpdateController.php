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
            Debe existir persistencia en algún lado 

            - Deben correr como las migraciones,... PERO....
            - La base de datos tiene que estar CONTENIDA en el código fuente => 

            Crear un archivo de texto por cada task ejecutada (emulando registros en 'migrations')
        */

        $update_path = static::$update_path . 'batches/';

        include $update_path . '000-migrations.php';
        // include $update_path . '005-some-model-changes.php';
        // include $update_path . '006-move-models.php';
        // include $update_path . '007-change-model-namespaces.php';
        // include $update_path . '008-delete-all-schemas.php';
        // include $update_path . '009-regenerate-all-schemas.php';
        // include $update_path . '010-some-model-changes.php';
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

