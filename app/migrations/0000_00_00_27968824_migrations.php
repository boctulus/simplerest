<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class Migrations implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        config()['db_connection_default'] = 'main';

        try {
            Model::query("
            CREATE TABLE `migrations` (
                `id` int(10) UNSIGNED NOT NULL,
                `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `created_at` DATETIME NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ");

            Model::query("
            ALTER TABLE `migrations`
                ADD PRIMARY KEY (`id`);");

            Model::query("
            ALTER TABLE `migrations`
                MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");    

            DB::commit(); 
    
        }catch(\Exception $e){
            try {
                DB::rollback();
            } catch (\Exception $e){

            }
            
            dd($e->getMessage(), "Transacción error");
        }	
    }
}

