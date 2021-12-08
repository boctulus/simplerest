<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class File implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        DB::setConnection('main');
		
		$sc = new Schema('files');
		$sc->renameColumn('locked', 'is_locked');
		$sc->alter();
		
    }

    public function down()
    {
        DB::setConnection('main');
		
		$sc = new Schema('files');
		$sc->renameColumn('is_locked', 'locked');
		$sc->alter();
		
    }
}

