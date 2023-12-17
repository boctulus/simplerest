<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class CommonSurnames implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        $sc = new Schema('common_surnames');

        $sc
        ->integer('id')->auto()->pri()
        ->varchar('text')->unique()->nullable()
        ->varchar('language', 20)->nullable()
        ->varchar('country', 20)->nullable()
        ->datetime('created_at');

		$sc->create();		
    }

    /**
	* Run undo migration.
    *
    * @return void
    */
    public function down()
    {
        $sc = new Schema('common_surnames');
        $sc->dropTableIfExists();
    }
}

