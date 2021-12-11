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

    function install(...$opt){
        $this->check();

        // ...
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

