<?php

use Boctulus\Simplerest\Core\Libs\DB;

class PerfTest
{
    public function up()
    {
        DB::statement("
        CREATE TABLE IF NOT EXISTS `perf_test` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(100) DEFAULT NULL,
            `email` varchar(150) DEFAULT NULL,
            `age` int(11) DEFAULT NULL,
            `status` varchar(20) DEFAULT NULL,
            `salary` decimal(12,2) DEFAULT NULL,
            `notes` text DEFAULT NULL,
            `created_at` datetime DEFAULT NULL,
            `updated_at` datetime DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }

    public function down()
    {
        DB::statement("DROP TABLE IF EXISTS `perf_test`");
    }
}


