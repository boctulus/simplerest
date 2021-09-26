<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

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

            Model::query("
            CREATE TABLE `email_notifications` (
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

            Model::query("
            ALTER TABLE `email_notifications`
                ADD PRIMARY KEY (`id`);
            ");

            Model::query("
            ALTER TABLE `email_notifications`
            MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
            ");
        });

    }
}

