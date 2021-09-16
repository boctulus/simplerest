<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class Webhooks implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        config()['db_connection_default'] = 'main';

        DB::transaction(function(){

            Model::query("
            CREATE TABLE `webhooks` (
                `id` int(11) NOT NULL,
                `name` varchar(50) DEFAULT NULL,
                `entity` varchar(50) NOT NULL,
                `op` varchar(10) NOT NULL,
                `conditions` varchar(1024) DEFAULT NULL,
                `callback` varchar(255) NOT NULL,
                `belongs_to` int(11) DEFAULT NULL,
                `created_at` datetime NOT NULL,
                `created_by` int(11) DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                `updated_by` int(11) DEFAULT NULL,
                `deleted_at` datetime DEFAULT NULL,
                `deleted_by` int(11) DEFAULT NULL
              ) ENGINE=InnoDB DEFAULT CHARSET=utf16;
            ");

            Model::query("
            ALTER TABLE `webhooks`
                ADD PRIMARY KEY (`id`);");

            Model::query("
            ALTER TABLE `webhooks`
                MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
            ");

        });

    }
}

