<?php

namespace simplerest\controllers;

use simplerest\core\ConsoleController;
use simplerest\libs\Hardware;
use simplerest\libs\StdOut;
use simplerest\libs\Files;
use simplerest\libs\Strings;
use simplerest\libs\Update;
use simplerest\libs\DB;

/*
    Update commands
*/

class UpdateController extends ConsoleController
{
    static public $update_path;
    
    function __construct()
    {
        $last_ver_dir = Update::getLastVersionDirectory();
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

    function index(){
        print_r("Do you mean 'php com update install' ?". PHP_EOL. PHP_EOL);
    }

    // protected
    function run_batches(){
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
                StdOut::pprint("[ Error ] Batch $file exited with error", true);
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

        // Si el copiado fue exitoso...... debe anotarse como completed !
        
        $ori =  static::$update_path . 'files';
        $dst = ROOT_PATH;

        Files::copy($ori, $dst);

        /*
            Run batches
        */

        $this->run_batches();

        Files::cp(static::$update_path . 'version.txt', $dst);
    }

    function list(){
        
        // ...
    }

    /*
        Debe hacer un "list" y comparar con "repo" remoto
    */
    function is_updated(){
        
    }

    function get(){
        
        // ...
    }

    /*
        Podría traerse la descripción de repos remotos si no estuviera instalado.
    */
    function description(...$opt){
        $o = $opt[0] ?? '--current';

        if (preg_match('/^(--current|current)$/', $o)){
            $desc = file_get_contents(static::$update_path . 'description.txt');
        }

        if (preg_match('/^(--version|version)[:|=](.*)$/', $o, $matches)){
            $version = $matches[2];


            foreach (new \DirectoryIterator(UPDATE_PATH) as $fileInfo) {
                if($fileInfo->isDot() || !$fileInfo->isDir()) continue;
                
                $dir = $fileInfo->getBasename();
                $ver = substr($dir, 11);

                if ($ver == $version){
                    $desc = file_get_contents(UPDATE_PATH . $dir . '/' . 'description.txt');
                }
            }    
        }

        if (!isset($desc)){
            d("There is no description available");
            exit;
        }

        d($desc);
    }

    function version(){
        $cur_ver = Update::getLastInstalledVersion();
        d($cur_ver, "Current version");
    }

    function status(){
        $cur_ver         = Update::getLastInstalledVersion();
        $last_ver_in_dir = Update::getLastVersionInDirectories();

        d($cur_ver, "Current version");

        if ($last_ver_in_dir > $cur_ver){
            $batches_path   = static::$update_path . 'batches/';
            $completed_path = static::$update_path . 'completed/';

            $batches           = glob($batches_path   . '*.php');
            $batches_completed = glob($completed_path . '*.php');

            if (count($batches) != 0 && count($batches_completed) < count($batches)){
                StdOut::pprint("There are pending batches for update $last_ver_in_dir");
                exit(1);
            } else {
                d($last_ver_in_dir, "Pending to install");
            }
            
        }
    }
}

