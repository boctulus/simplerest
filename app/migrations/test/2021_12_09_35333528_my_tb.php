<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class MyTable implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        $sc = new Schema('my_tb');
		$sc->dropPrimary();
		$sc->unique('campo8','campo_otro');
		$sc->alter();
		
    }
}
