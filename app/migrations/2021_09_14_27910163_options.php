<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class Options implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        $sc = new Schema('options');
        
        $sc
        // No esta agregando la PRI KEY !!!!
        ->varchar('the_key')
        ->varchar('the_val', 240)
        ->datetime('created_at')
        ->datetime('updated_at');

		$sc->create();		

        $sc->addPrimary('the_key');
        $sc->alter();
    }

    /**
	* Run undo migration.
    *
    * @return void
    */
    public function down()
    {
        Schema::dropIfExists(
            'options'
        );
    }
}

