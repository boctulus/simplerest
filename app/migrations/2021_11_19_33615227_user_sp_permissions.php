<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class UserSpPermissionsDateTimeChanged implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        $sc = new Schema('user_sp_permissions');

        $sc->datetime('created_at')->nullable();
        $sc->alter();
    }

    public function down(){
        $sc = new Schema('user_sp_permissions');

        $sc->datetime('created_at')->notNullable();
        $sc->alter();
    }
}

