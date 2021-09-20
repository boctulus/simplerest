<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class Users implements IMigration
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
            CREATE TABLE `users` (
                `id` int(11) NOT NULL,
                `firstname` varchar(50) DEFAULT NULL,
                `lastname` varchar(80) DEFAULT NULL,
                `username` varchar(15) NOT NULL,
                `password` varchar(60) DEFAULT NULL,
                `active` tinyint(1) DEFAULT NULL,
                `locked` tinyint(1) NOT NULL DEFAULT '0',
                `email` varchar(60) NOT NULL,
                `confirmed_email` tinyint(1) DEFAULT '0',
                `belongs_to` int(11) DEFAULT NULL,
                `created_by` int(11) DEFAULT NULL,
                `updated_by` int(11) DEFAULT NULL,
                `deleted_by` int(11) DEFAULT NULL,
                `created_at` datetime NOT NULL,
                `updated_at` datetime DEFAULT NULL,
                `deleted_at` datetime DEFAULT NULL
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ");

            Model::query("
            ALTER TABLE `users`
                ADD PRIMARY KEY (`id`),
                ADD KEY `belongs_to` (`belongs_to`),
                ADD KEY `created_by` (`created_by`),
                ADD KEY `deleted_by` (`deleted_by`),
                ADD KEY `updated_at` (`updated_at`),
                ADD KEY `updated_by` (`updated_by`);"
            );

            Model::query("
            ALTER TABLE `users`
                MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;"
            );   
                
            Model::query("ALTER TABLE `users`
                ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`belongs_to`) REFERENCES `users` (`id`),
                ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
                ADD CONSTRAINT `users_ibfk_3` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`),
                ADD CONSTRAINT `users_ibfk_4` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`);"
            );

            DB::commit(); 
    
        }catch(\Exception $e){
            try {
                DB::rollback();
            } catch (\Exception $e){
                dd($e->getMessage(), "Transacción error");
                exit;
            }
        }	
    }
}

