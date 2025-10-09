<?php

use Boctulus\Simplerest\Core\Interfaces\IMigration;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\Schema;
use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Libs\DB;

class Users implements IMigration
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
        CREATE TABLE IF NOT EXISTS `users` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `firstname` varchar(50) DEFAULT NULL,
            `lastname` varchar(80) DEFAULT NULL,
            `username` varchar(15) NOT NULL,
            `password` varchar(60) DEFAULT NULL,
            `is_active` tinyint(1) DEFAULT NULL,
            `is_locked` tinyint(1) NOT NULL DEFAULT '0',
            `email` varchar(60) NOT NULL,
            `confirmed_email` tinyint(1) DEFAULT '0',
            `address` varchar(240) DEFAULT NULL,
            `belongs_to` int(11) DEFAULT NULL,
            `created_by` int(11) DEFAULT NULL,
            `updated_by` int(11) DEFAULT NULL,
            `deleted_by` int(11) DEFAULT NULL,
            `created_at` datetime NOT NULL,
            `updated_at` datetime DEFAULT NULL,
            `deleted_at` datetime DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `email` (`email`),
            UNIQUE KEY `username` (`username`),
            KEY `belongs_to` (`belongs_to`),
            KEY `created_by` (`created_by`),
            KEY `deleted_by` (`deleted_by`),
            KEY `updated_at` (`updated_at`),
            KEY `updated_by` (`updated_by`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        // Add foreign keys only if they don't exist yet
        try {
            DB::statement("ALTER TABLE `users`
                ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`belongs_to`) REFERENCES `users` (`id`),
                ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
                ADD CONSTRAINT `users_ibfk_3` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`),
                ADD CONSTRAINT `users_ibfk_4` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`);"
            );
        } catch (\Exception $e) {
            // Ignore if foreign keys already exist
        }
    }

    /**
     * Rollback migration.
     *
     * @return void
     */
    public function down()
    {
        get_default_connection();
        DB::statement("DROP TABLE IF EXISTS `users`;");
    }
}

