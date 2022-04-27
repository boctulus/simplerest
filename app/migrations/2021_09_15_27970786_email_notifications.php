<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class EmailNotifications implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        get_default_connection();

        DB::transaction(function(){

            DB::statement("
            CREATE TABLE IF NOT EXISTS `email_notifications` (
                `id` int(11) NOT NULL,
                `from_addr` varchar(320) DEFAULT NULL,
                `from_name` varchar(80) DEFAULT NULL,
                `to_addr` varchar(320) NOT NULL,
                `to_name` varchar(80) DEFAULT NULL,
                `cc_addr` varchar(320) DEFAULT NULL,
                `cc_name` varchar(80) DEFAULT NULL,
                `bcc_addr` varchar(320) DEFAULT NULL,
                `bcc_name` varchar(80) DEFAULT NULL,
                `replyto_addr` varchar(320) DEFAULT NULL,
                `subject` varchar(80) NOT NULL,
                `body` text,
                `sent_at` datetime DEFAULT NULL,
                `created_at` datetime NOT NULL,
                `deleted_at` datetime DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ");

            DB::statement("
            ALTER TABLE `email_notifications`
                ADD PRIMARY KEY (`id`);
            ");

            DB::statement("
            ALTER TABLE `email_notifications`
            MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
            ");
        });

    }
}

