<?php

use Boctulus\Simplerest\Core\Interfaces\IMigration;
use Boctulus\Simplerest\Core\Libs\Schema;
use Boctulus\Simplerest\Core\Libs\DB;

class AddNameToUsers implements IMigration
{
    public function up()
    {
        if (!Schema::hasColumn('users', 'name')) {
            DB::statement("ALTER TABLE `users` ADD COLUMN `name` varchar(191) DEFAULT NULL AFTER `username`;");
            DB::statement("UPDATE `users` SET `name` = TRIM(CONCAT(COALESCE(`firstname`, ''), ' ', COALESCE(`lastname`, ''))) WHERE `name` IS NULL OR `name` = '';");
        }
    }

    public function down()
    {
        if (Schema::hasColumn('users', 'name')) {
            DB::statement("ALTER TABLE `users` DROP COLUMN `name`;");
        }
    }
}
