<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class Products implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        DB::statement("
        CREATE TABLE `products` (
            `id` int(11) NOT NULL,
            `name` varchar(80) NOT NULL,
            `type` varchar(20) DEFAULT NULL,
            `regular_price` float DEFAULT NULL,
            `sale_price` float DEFAULT NULL,
            `description` text NOT NULL,
            `short_description` varchar(512) DEFAULT NULL,
            `slug` varchar(100) NOT NULL,
            `images` json NOT NULL,
            `categories` varchar(250) DEFAULT NULL,
            `tags` varchar(250) DEFAULT NULL,
            `dimensions` json DEFAULT NULL,
            `attributes` json DEFAULT NULL,
            `sku` varchar(50) DEFAULT NULL,
            `status` varchar(20) DEFAULT NULL,
            `stock` int(11) DEFAULT NULL,
            `stock_status` varchar(30) DEFAULT NULL,
            `url_ori` varchar(300) DEFAULT NULL,
            `posted` tinyint(1) DEFAULT NULL,
            `comment` varchar(200) DEFAULT NULL,
            `created_at` datetime NOT NULL,
            `updated_at` datetime DEFAULT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");		

        DB::statement("
            ALTER TABLE `products`
            ADD PRIMARY KEY (`id`),
            ADD UNIQUE KEY `slug` (`slug`),
            ADD UNIQUE KEY `url_ori` (`url_ori`),
            ADD KEY `sku` (`sku`);
        ");

        DB::statement("
            ALTER TABLE `products`
            MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
        ");
    }
}

