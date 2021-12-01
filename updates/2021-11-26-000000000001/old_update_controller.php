<?php

namespace simplerest\controllers;

use simplerest\core\ConsoleController;
use simplerest\libs\Hardware;
use simplerest\libs\Files;
use simplerest\libs\Strings;
use simplerest\libs\Schema;
use simplerest\libs\DB;

/*
    Para uso por usuarios
*/

class UpdateController extends ConsoleController
{
    function __construct()
    {
        parent::__construct();     
        $this->setup();   
    }


    /*
        Ensayo mover todos los modelos a donde corresponde según su "tenant group"

        Para evitar:
        Uncaught Error: Class '\simplerest\models\main\SpPermissionsModel' not found

        Además debe modificarse cada namespace (eso se puede hacer en otro proceso)
    */
    function mv_models(){
        $db_representants = [
            'legion' => 'db_flor'
        ];

        /* 
            Creo estructura 
        */
        $groups  = DB::getTenantGroupNames();

        foreach ($groups as $g){
            Files::mkDir(MODELS_PATH . $g);
        }

        Files::mkDir(MODELS_PATH . get_default_connection_id());

        /*
            Muevo modelos
        */

        $grouped = DB::getDatabasesGroupedByTenantGroup(true);
        //dd($grouped);
        
        foreach ($grouped as $group_name => $db_name){
            // elijo la conexión a una DB cualquiera de cada grupo como representativa
            // o la que me especifiquen
            $db_conn = $db_name[0];

            if (isset($db_representants) && !empty($db_representants)){
                if (isset($db_representants[$group_name])){
                    $db_conn = $db_representants[$group_name];
                }
            } 

            $tables = Schema::getTables($db_conn);
            
            foreach ($tables as $tb){
                $model_name = Strings::snakeToCamel($tb) . 'Model';
                $filename   = "$model_name.php";
                $ori_path   = MODELS_PATH . $filename;
                $dst_path   = MODELS_PATH . $group_name . DIRECTORY_SEPARATOR . $filename;

                //dd([$ori_path, $dst_path]);

                if (!file_exists($ori_path)){
                    continue;
                }

                $ok = rename($ori_path, $dst_path);
            }
        }
    }

    function change_model_namespaces()
    {
        $paths = Files::recursiveGlob(MODELS_PATH . '/*.php');

        foreach ($paths as $path){
            if (Strings::endsWith('MyModel.php', $path)){
                continue;
            }
        
            $stripped_path  = Strings::diff($path, MODELS_PATH);
            $folder = Strings::match($stripped_path, '~([a-z0-9_]+)/~');

            if (!empty($folder)){
                $file = file_get_contents($path);
                Strings::replace('namespace simplerest\models;', "namespace simplerest\models\\$folder;", $file);
                $ok = file_put_contents($path, $file);
            }             
        }
    }


    function setup(){
        /*
            Descargar ZIP y descomprimirlo en el raíz
        */

        // .... (hacer)

        /*
            Verificar NO esté corriendo en mi PC para evitar un desastre
        */
        
        $id = Hardware::UniqueMachineID();

        if (ROOT_PATH == '/home/www/simplerest/' && $id == 'd57b457667c91f55e9dee697950e5d04'){
            dd("Running at Home. Aborting (..)");
            exit;
        }

        /*
            Cambio DB_DATABASE por DB_NAME 
            como variable de entorno
        */

        $filenames = [
            '.env',
            'config/config.php',
            'app/helpers/db_dynamic_load.php'
        ];

        foreach ($filenames as $f){
            Files::replace(ROOT_PATH . $f, 'DB_DATABASE=', 'DB_NAME=');
            Files::replace(ROOT_PATH . $f, "env('DB_DATABASE')", "env('DB_NAME')");
        }

        /*
            Borrar la carpeta /docs/dev

        */


        $ok = Files::delTree(ROOT_PATH . 'docs/dev');
        

        /*
             Modificar el config.php agregando la nueva sección para tenant groups
        */

        $new = "
        'tentant_groups' => [
            'legion' => [
                'db_[0-9]+',
                'db_legion',
                'db_flor'
            ]
        ],";

        $path = CONFIG_PATH . 'config.php';

        $file = file_get_contents($path);

        if (!Strings::contains('tentant_groups', $file)){
            $needle = "'db_connection_default' => 'main',";
            $pos = strpos($file, $needle);
            $pos += strlen($needle);

            $left  = Strings::left($file,  $pos);
            $right = Strings::right($file, $pos);

            $file = $left . PHP_EOL . Strings::tabulate($new, -4) . $right;

            print_r("Updating config.php\r\n");
            file_put_contents($path, $file);  
        }
        

        /*
            MakeControllerBase::hideResponse();
            MigrationsController::hideResponse();

            por 

            \simplerest\libs\StdOut::hideResponse();
        */

        $patts = [
            CONTROLLERS_PATH . '/*.php',
            LIBS_PATH . '/*.php',
            MODELS_PATH . '/*.php',
        ];

        $files = [];

        foreach ($patts as $p){
            $files = array_merge($files, Files::recursiveGlob($p));
        }

        foreach ($files as $path){
            $file = file_get_contents($path);

            if ($file == __FILE__){
                continue;
            }

            Strings::replace('MakeControllerBase::hideResponse();', 
                            '\simplerest\libs\StdOut::hideResponse();', $file);

            Strings::replace('MigrationsController::hideResponse();', 
                            '\simplerest\libs\StdOut::hideResponse();', $file);

            file_put_contents($path, $file);
        }


        /*
            Cambios en modelos

        */

        $paths = Files::recursiveGlob(MODELS_PATH . '/*.php');

        foreach ($paths as $path){
            if (Strings::endsWith('MyModel.php', $path)){
                continue;
            }

            $file = file_get_contents($path);
        
            $file = preg_replace('~new ([^(]+)~', "$1::class", $file);
            $file = str_replace('::class()', '::class', $file);

            /*
                 + otros cambios
            */

            // Agrego use simplerest\models\MyModel
            if (!Strings::contains('use simplerest\models\MyModel;', $file)){
                $lines = explode(PHP_EOL, $file);
    
                foreach ($lines as $ix => $line){
                    $line = trim($line);
    
                    if (Strings::startsWith('use ', $line)){
                        $lines[$ix] = 'use simplerest\models\MyModel;' . PHP_EOL . $line ;
                        break;
                    }
                }
    
                $file = implode(PHP_EOL, $lines); 
            }

            $ok = file_put_contents($path, $file);
        }

        /*
            Muevo modelos a su nueva ubicación
        */

        $this->mv_models();


        /*
            Corrijo namespace de cada modelo en carpetas
        */

        $this->change_model_namespaces();


        /*
            Borrar todos los schemas

        */

        $ok = Files::deleteAll(SCHEMA_PATH, '*.php');
        
        /*
            Re-generar todos los schemas

        */
        
        $mk = new MakeController();

        $re_gen_schemas = function($tenant_id) use ($mk){
            $mk->any("all", "-s", "-f", "--unignore", "--from:$tenant_id");
            $mk->pivot_scan("--from:$tenant_id");
        };


        $conn_ids = DB::getConnectionIds();

        foreach ($conn_ids as $cid){
            $re_gen_schemas($cid);
        }

        /* 
            Parsear todos los archivos de modelo de la base de datos de "legion"
            ajustando todos los namespaces
        */

        $ignored_tables = Schema::getTables('main');

        $ignored_models = [
            'MyModel.php'
        ];

        foreach ($ignored_tables as $it){
            $ignored_models[] = Strings::snakeToCamel($it). 'Model.php';
        }

        $dir = MODELS_PATH;
        foreach (new \DirectoryIterator($dir) as $fileInfo) {
            if($fileInfo->isDot()  || $fileInfo->isDir()) continue;

            $filename  = $fileInfo->getFilename();

            if (in_array($filename, $ignored_models)){
                continue;
            }

            $full_path = $fileInfo->current()->getPathname();

            $file = file_get_contents($full_path);

            if (Strings::contains('\\legion\\', $file)){
                continue;
            }

            if (Strings::contains('\\az\\', $file)){
                continue;
            }

            $patt = 'use simplerest\schemas\\';
            $patt = '~' . preg_quote($patt) . '([a-zA-z0-9_]+);' . '~';

            $file = preg_replace($patt,  preg_quote('use simplerest\schemas\\legion\\'). "$1;", $file);

            print_r("Updating $filename\r\n");
            file_put_contents($dir. DIRECTORY_SEPARATOR . $filename, $file);
        }  
           
    }


}

