<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

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

    // public function down(){
    //     $sc = new Schema('user_tb_permissions');

    //     $sc->datetime('created_at')->notNullable();
    //     $sc->alter();
    // }
}

