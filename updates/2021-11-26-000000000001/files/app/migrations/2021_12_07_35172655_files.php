<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class Files implements IMigration
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
		$sc = new Schema('files');
		$sc->renameColumn('locked', 'is_locked');
		$sc->alter();
		
    }

    public function down()
    {		
		$sc = new Schema('files');
		$sc->renameColumn('is_locked', 'locked');
		$sc->alter();
		
    }
}

