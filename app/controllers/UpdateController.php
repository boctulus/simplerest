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
    // This PATH will be AUTO-GENERATED
    static public $update_path = ROOT_PATH . 'updates/2021-11-26-000000000001/';

    // function make($name, ...$opt) {
    //     return (new MakeController)->update($name, $opt);
    // }

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

    protected function run_tasks(){
        /*
            Debe existir persistencia en algún lado SQLIte, archivo de texto,....
            ... donde guardar QUE tareas ya fueron ejecutadas para evitar
            correrlas dos veces !

            Deben correr como las migraciones!
        */

        $update_path = static::$update_path . 'tasks/';

        // include $update_path . '005-some-model-changes.php';
        // include $update_path . '006-move-models.php';
        // include $update_path . '007-change-model-namespaces.php';
        //include $update_path . '008-delete-all-schemas.php';
        //include $update_path . '009-regenerate-all-schemas.php';
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
            Run tasks
        */

        $this->run_tasks();
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

