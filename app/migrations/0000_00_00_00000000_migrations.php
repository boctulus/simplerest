<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class Migrations implements IMigration
{
    function __construct(){
        get_default_connection();
    }
    
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        DB::beginTransaction();
        
        try {
            DB::disableForeignKeyConstraints();

            $driver = DB::driver();

            switch ($driver){
                case 'sqlite':
                    Model::query("
                    CREATE TABLE IF NOT EXISTS migrations (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        db varchar(50) DEFAULT NULL,
                        filename varchar(255) NOT NULL,
                        created_at DATETIME NULL
                    );");
                    break;

                case 'mysql':
                    Model::query("
                    CREATE TABLE IF NOT EXISTS `migrations` (
                        `id` int(11) PRIMARY KEY NOT NULL,
                        `db` varchar(50) DEFAULT NULL,
                        `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                        `created_at` DATETIME NULL
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                    ");

                    Model::query("
                    ALTER TABLE `migrations`
                        MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");    

                break;

                default:
                 throw new \InvalidArgumentException("Driver $driver is not fully supported for migrations");   
            }

            
            DB::commit(); 
    
        }catch(\Exception $e){
            try {
                DB::rollback();
                throw $e;
            } catch (\Exception $e){
                d($e->getMessage(), "Transaction error");
                throw $e;
            }
        } finally {
            DB::enableForeignKeyConstraints();
        }	
    }
}

