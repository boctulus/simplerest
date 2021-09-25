<?php

namespace simplerest\controllers;

use simplerest\core\Controller;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\libs\Schema;
use simplerest\libs\Factory;
use simplerest\libs\DB;
use simplerest\libs\Strings;
use simplerest\libs\Debug;
use simplerest\models\MigrationsModel;

class MigrationsController extends Controller
{
    function make($name, ...$opt) {
        return (new MakeController)->migration($name, $opt);
    }
    
    /*
        Implementar --force para ejecutar en migración

        Migrating: 2014_10_12_000000_create_users_table
        Migrated:  2014_10_12_000000_create_users_table
        Migrating: 2014_10_12_100000_create_password_resets_table
        Migrated:  2014_10_12_100000_create_password_resets_table
        Migrating: 2020_10_28_145609_as_d_f
        Migrated:  2020_10_28_145609_as_d_f

        php com migrations migrate --file=2021_09_14_27905675_user_sp_permissions.php

    */
    function migrate(...$opt) {
        $filenames = [];

        $file_opt  = false;
        $to_db     = null;
    
        $path = MIGRATIONS_PATH . DIRECTORY_SEPARATOR;

        foreach ($opt as $o){
            if (preg_match('/^--to[=|:]([a-z][a-z0-9A-Z_]+)$/', $o, $matches)){
                $to_db = $matches[1];
            }

            if ( Strings::startsWith('--file=', $o)){
                $file_opt = true;

                $_f = substr($opt[0], 7);
                            
                if (Strings::contains(DIRECTORY_SEPARATOR, $_f)){
                    $fr = explode(DIRECTORY_SEPARATOR, $_f);

                    $_f = $fr[count($fr)-1];

                    unset($fr[count($fr)-1]);
                    $path = implode(DIRECTORY_SEPARATOR, $fr) . DIRECTORY_SEPARATOR;

                    if (!Strings::startsWith(DIRECTORY_SEPARATOR, $path)){
                        $path = MIGRATIONS_PATH . DIRECTORY_SEPARATOR . $path;
                    }
                } 
                
                $filenames = [ $_f ];
            } 
        }
        
        if (!$file_opt){
            foreach (new \DirectoryIterator(MIGRATIONS_PATH) as $fileInfo) {
                if($fileInfo->isDot()  || $fileInfo->isDir()) continue;
                $filenames[] = $fileInfo->getFilename();
            }   
    
            asort($filenames);    
        }

        foreach ($filenames as $filename) {
            if (Schema::hasTable('migrations') && (new MigrationsModel(true))->where(['filename' => $filename])->exists()){
                continue;
            }

            $st = get_declared_classes();

            $full_path = str_replace('//', '/', $path . $filename);
            require_once $full_path;
            
            $class_name = array_values(array_diff_key(get_declared_classes(),$st))[0];

            if (!class_exists($class_name)){
                throw new \Exception ("Class '$class_name' does not exists in $filename");
            }

            echo "Migrating '$filename'\r\n";

            if (!in_array('--simulate', $opt)){
                if (!empty($to_db)){
                    DB::setConnection($to_db);
                }

                (new $class_name())->up();
            } else {
                continue;
            }
            
            echo "Migrated  '$filename' --ok\r\n";
            
            /*
                Main connection restore
            */
            DB::setConnection('main');

            DB::table('migrations')->create([
                'filename' => $filename
            ]);
        }         
    }

    /*
        Regresa migraciones (por defecto solo una)

        Debe ir borrando registros de `migrations` excepto una migración tenga batch=0 en cuyo caso la saltea
    */
    function rollback(...$opt) 
    {
        $steps = 1;
        if (isset($opt[0]) && $opt[0] !== NULL){
            $steps = Strings::slice($opt[0], '/^--step=([0-9]+)$/');
            
            if ($opt[0] == '--all'){
                $steps = PHP_INT_MAX;
            }
        }

        $filenames = [];
        foreach (new \DirectoryIterator(MIGRATIONS_PATH) as $fileInfo) {
            if($fileInfo->isDot()) continue;
            $filenames[] = $fileInfo->getFilename();
        }    

        $filenames = array_reverse($filenames);

        $cnt = min($steps, count($filenames));
        for ($i=0; $i<$cnt; $i++){
            $filename   = $filenames[$i];            
            $class_name = Strings::snakeToCamel(substr(substr($filename,18),0,-4));

            require_once MIGRATIONS_PATH . DIRECTORY_SEPARATOR . $filename;

            if (!class_exists($class_name)){
                throw new \Exception ("Class '$class_name' does not exists in $filename");
            }

            echo "Rolling back '$filename'\r\n";
            (new $class_name())->down();
            echo "Rolled back  '$filename' --ok\r\n";
        }
    }

    /*
        Rollback de todas las migraciones. Equivale a "rollback --all"
    */
    function reset() {
        $this->rollback("--all");
    }

    /*
        The command will drop all tables from the database (incluso las que no están afectadas por las migraciones) and then execute the "migrate" command

        --seed corre todos los db seeders

        Dropped all tables successfully. * 
        Migration table created successfully. *
        Migrating: 2014_10_12_000000_create_users_table
        Migrated:  2014_10_12_000000_create_users_table
        Migrating: 2014_10_12_100000_create_password_resets_table
        Migrated:  2014_10_12_100000_create_password_resets_table

        <--- para no usar el método down()

        Nota: Comienza haciendo un DROP DATABASE

    */
    function fresh(...$opt) 
    {   
        $config = config();

        $force = false;
        $conn_id = $config['db_connection_default'];

        foreach ($opt as $o){
            if ($o == '--force'){
                $force = true;
                continue;
            }

            $_conn = Strings::slice($o, '/^--from[=|:]([a-zA-Z][0-9a-zA-Z]+)$/');
            if (!empty($_conn)){
                $conn_id = $_conn;
                continue;
            }
        }

        if (!$force){
            echo "fresh: this method is destructive. Every table for '$conn_id' will be dropped. Please use option --force if you want to procede.\r\n";
            exit;
        }

        if ($conn_id == NULL || !isset($config['db_connections'][$conn_id])){
            throw new \Exception("Connection Id '$conn_id' not defined");
        }

        Schema::FKcheck(0);
        
        config()['db_connection_default'] = $conn_id;
        $conn = DB::getConnection();  

        $tables = Schema::getTables();
        
        try{
            foreach($tables as $table) {
                echo "Dropping table '$table'\r\n";
                $st = $conn->prepare("DROP TABLE IF EXISTS `$table`;");
                $res = $st->execute();
                echo "Dropped table  '$table' --ok\r\n";
            }

            $this->migrate();
        } catch (\PDOException $e) {    
            dd("DROP TABLE for `$table` failed", "PDO Error");
            dd($e->getMessage(), "MSG"); 
            throw $e;
        } catch (\Exception $e) {   
            dd($e->getMessage(), "MSG");
            throw $e;
        }	             

        Schema::FKcheck(1);
    }

    /*
        Rolling back: 2014_10_12_100000_create_password_resets_table
        Rolled back:  2014_10_12_100000_create_password_resets_table
        Rolling back: 2014_10_12_000000_create_users_table
        Rolled back:  2014_10_12_000000_create_users_table
        Migrating: 2014_10_12_000000_create_users_table
        Migrated:  2014_10_12_000000_create_users_table
        Migrating: 2014_10_12_100000_create_password_resets_table
        Migrated:  2014_10_12_100000_create_password_resets_table

        Lo que hace exactamente es un reset() seguido un migrate()
    */
    function refresh() {
        $this->reset();
        $this->migrate();
    }
}

