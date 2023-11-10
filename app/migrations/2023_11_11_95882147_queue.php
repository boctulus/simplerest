<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class Queue implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        $sc = new Schema('queue');

        $sc
        ->integer('id')->auto()->pri()
        ->json('data')
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
        ### DOWN

        $sc = new Schema('queue');
        $sc->dropTableIfExists()->alter();
    }
}

