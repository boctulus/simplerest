<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class UpdateTasks implements IMigration
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
        $sc
        ->varchar('uuid', 36)->pri()  /* no está creando la PRI KEY */
        ->varchar('filename', 50)
        ->datetime('created_at');

		$sc->create();		
    }

    function down(){
        $sc = new Schema('update_tasks');
        $sc->dropTableIfExists()
        ->alter();
    }
}

