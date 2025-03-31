<?php

use Boctulus\Simplerest\Core\Interfaces\IMigration;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\Schema;
use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Libs\DB;

class UserTbPermissions implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        $sc = new Schema('user_tb_permissions');

        $sc->datetime('created_at')->nullable();
        $sc->alter();
    }

    public function down(){
        $sc = new Schema('user_tb_permissions');

        $sc->datetime('created_at')->notNullable();
        $sc->alter();
    }
}

