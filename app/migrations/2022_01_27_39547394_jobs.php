<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class Jobs implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        $sc = new Schema('jobs');
        $sc
        ->int('id')->primary()->auto()
        ->blob('object')
        ->blob('params')
        ->datetime('created_at')
        ;
		$sc->create();
		
    }
}

