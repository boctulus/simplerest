<?php

namespace simplerest\controllers;

use simplerest\core\ConsoleController;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\libs\Factory;
use simplerest\libs\DB;
use simplerest\libs\Files;
use simplerest\libs\Strings;

/*
    Clase que gestiona acutalizaciones del framework

    NO usar ****
    
    Está en desarrollo.
*/

class UpdateController extends ConsoleController
{
    protected $ori, $dst;

    function __construct()
    {
        parent::__construct();     
        $this->setup();   
    }

    function cp($ori, $dst, bool $simulate = false){
        $ori = trim($ori);
        $dst = trim($dst);

        echo "Copying $ori > $dst\r\n";

        if (!$simulate){
            $ok = copy($ori, $dst);
        } else {
            $ok = true;
        }       

        if ($ok){
            echo "-- ok\r\n\r\n";
        } else {
            echo "-- FAILED ! \r\n\r\n";
        }

        return $ok;
    }

    /*
        Ojo! proteger archivo

        /app/helpers/db_dynamic_load.php
    */
    function setup(){
        $this->ori = '/home/www/simplerest';
        $this->dst = '/home/www/html/dsi_legion_simple_rest';

        $str_files = <<<'FILES'
        app/core
        app/exceptions
        app/helpers
        app/interfaces
        app/libs
        app/migrations
        app/locale        
        app/traits
        app/controllers/MakeController.php
        app/controllers/MigrationsController.php
        app/controllers/MyApiController.php
        app/controllers/TestController.php
        #app/controllers/MyAuthController.php
        packages
        docs
        config/middlewares.php
        app/core/Controller.php
        FILES;

        $files = explode(PHP_EOL, $str_files);

        $this->copy_new_files($files, [
            'db_dynamic_load.php'
        ]);

        $to = $this->dst . '/app/models';
        foreach (new \DirectoryIterator($to) as $fileInfo) {
            if($fileInfo->isDot()  || $fileInfo->isDir()) continue;

            $filename  = $fileInfo->getFilename();
            $full_path = $fileInfo->current()->getPathname();

            $file = file_get_contents($full_path);

            $file = str_replace('extends Model', 'extends MyModel', $file);

            print_r("Updating $filename\r\n");
            file_put_contents($to. DIRECTORY_SEPARATOR . $filename, $file);
        }  
        
        echo "\r\n";

        $this->copy_new_files([
            'app/models/MyModel.php'
        ]);
    }

    /*
        @param  files a copiar
        @except files a excluir (de momento sin ruta)
    */
    function copy_new_files(Array $files, Array $except = [])
    {
        $ori = $this->ori;
        $dst = $this->dst;

        foreach ($files as $_file){
            $file = trim($_file);

            if (Strings::startsWith('#', $_file) || Strings::startsWith(';', $_file)){
                continue;
            }
            
            $ori_path = trim($ori . DIRECTORY_SEPARATOR . $_file);
    

            if (is_dir($ori_path)){
                Files::mkdir_ignore($dst . $file);

                $dir  = new \RecursiveDirectoryIterator($ori_path, \RecursiveDirectoryIterator::SKIP_DOTS);
                $it = new \RecursiveIteratorIterator($dir, \RecursiveIteratorIterator::SELF_FIRST);

                foreach ($it as $file) {
                    $file      = $file->getFilename();
                    $full_path = $it->current()->getPathname();
        
                    if (in_array($file, $except)){
                        dd("Skiping $file");
                        continue;
                    }

                    $indent = str_repeat('   ', $it->getDepth());
                    //echo $indent, " ├ $file\n";

                    $dif = Strings::substract($full_path, $ori);

                    $dst_path =  trim($dst . $dif);

                    if (is_dir($full_path)){
                        $path = pathinfo($dst_path);

                        $needed_path = Strings::substract($full_path, $ori_path);
                        $dirs = explode(DIRECTORY_SEPARATOR, $needed_path);
                        
                        $p = $dst . DIRECTORY_SEPARATOR . $_file;
            
                        foreach ($dirs as $dir){
                            if ($dir == ''){
                                continue;
                            }

                            $p .=  DIRECTORY_SEPARATOR . $dir;
                            
                            //print_r("Intentando crear dir '$p'\r\n");
                            Files::mkdir_ignore($p);
                        }
                        
                        // no se pude copiar un directorio, solo archivos
                        continue;
                    }

                    //print_r("Intentando copiar '$full_path' > '$dst_path'\r\n");
                    $this->cp($full_path, $dst_path);
                    #dd("---------------------------");
                }
                
                continue;
               
            }

            $this->cp($ori_path, $dst . DIRECTORY_SEPARATOR . $_file);
        }


    }

}

