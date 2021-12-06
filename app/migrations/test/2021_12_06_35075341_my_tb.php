<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class MyTb implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        $sc = new Schema('my_tb');
		$sc->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('notNull');
		$sc->alter();
		
    }
}

