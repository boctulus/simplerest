<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class FilesRenameLocked implements IMigration
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

