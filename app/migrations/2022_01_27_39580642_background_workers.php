<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class BackgroundWorkers implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        $sc = new Schema('background_workers');
        
        $sc->int('id')->pri()->auto();
        $sc->varchar('queue')->nullable()->index();
        $sc->int('pid', 5)->unique(); 
        $sc->datetime('created_at');
		$sc->create();	
		
    }
}

