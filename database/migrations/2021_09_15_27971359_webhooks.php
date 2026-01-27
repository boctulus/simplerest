<?php

use Boctulus\Simplerest\Core\Interfaces\IMigration;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\Schema;
use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Libs\DB;

class Webhooks implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        get_default_connection();

        DB::statement("
        CREATE TABLE IF NOT EXISTS `webhooks` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
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
            `deleted_by` int(11) DEFAULT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf16;
        ");
    }

    /**
     * Rollback migration.
     *
     * @return void
     */
    public function down()
    {
        get_default_connection();
        DB::statement("DROP TABLE IF EXISTS `webhooks`;");
    }
}

