<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

/*
    Cola de uso general. 
    
    No esta relacionada (en principio) con jobs o tareas en background.
*/
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
        ->varchar('category', 25)->nullable()
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

        Schema::dropIfExists('queue');
    }
}

