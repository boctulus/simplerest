<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class SpPermissions implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        DB::getDefaultConnection();

        DB::transaction(function(){

            Model::query("
            CREATE TABLE `sp_permissions` (
                `id` int(11) NOT NULL,
                `name` varchar(45) NOT NULL
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
            );

            Model::query("
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

            Model::query("
            ALTER TABLE `sp_permissions` ADD PRIMARY KEY(`id`);
            ");

            Model::query("
            ALTER TABLE `sp_permissions`
            MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");        
        });
    }
}

