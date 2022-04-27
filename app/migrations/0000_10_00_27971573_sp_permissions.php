<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class SpPermissions implements IMigration
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
        DB::transaction(function(){

            DB::statement("
            CREATE TABLE `sp_permissions` (
                `id` int(11) NOT NULL,
                `name` varchar(45) NOT NULL
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
            );

            DB::statement("
            INSERT INTO `sp_permissions` (`id`, `name`) VALUES
            (1, 'read_all'),
            (2, 'read_all_folders'),
            (3, 'read_all_trashcan'),
            (4, 'write_all'),
            (5, 'write_all_folders'),
            (6, 'write_all_trashcan'),
            (7, 'write_all_collections'),
            (8, 'fill_all'),
            (9, 'grant'),
            (10, 'impersonate'),
            (11, 'lock'),
            (12, 'transfer');"
            );

            DB::statement("
            ALTER TABLE `sp_permissions` ADD PRIMARY KEY(`id`);
            ");

            DB::statement("
            ALTER TABLE `sp_permissions`
            MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");        
        });
    }
}

