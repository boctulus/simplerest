<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class UpdateTaskMakePrimary implements IMigration
{
    function __construct(){
        get_default_connection();
    }

    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
		$sc = new Schema('update_tasks');
		$sc->field('uuid')->primary();
		$sc->alter();		
    }

    function down(){
        $sc = new Schema('update_tasks');
		$sc->field('uuid')->dropPrimary();
		$sc->alter();		
    }
}

