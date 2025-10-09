<?php

use Boctulus\Simplerest\Core\Interfaces\IMigration;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\Schema;
use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Libs\DB;

class MigrationsMigration implements IMigration
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
        $driver = DB::driver();

        switch ($driver){
            case 'sqlite':
                DB::statement("
                CREATE TABLE IF NOT EXISTS migrations (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    db varchar(50) DEFAULT NULL,
                    filename varchar(255) NOT NULL,
                    created_at DATETIME NULL
                );");
                break;

            case 'mysql':
                // Create table with AUTO_INCREMENT from the start
                DB::statement("
                CREATE TABLE IF NOT EXISTS `migrations` (
                    `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
                    `db` varchar(50) DEFAULT NULL,
                    `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                    `created_at` DATETIME NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                ");

                // Fix existing tables that were created without AUTO_INCREMENT
                try {
                    DB::statement("ALTER TABLE `migrations` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
                } catch (\Exception $e) {
                    // Ignore if already has AUTO_INCREMENT
                }

            break;

            default:
                throw new \InvalidArgumentException("Driver $driver is not fully supported for migrations");   
        }
    }
}

