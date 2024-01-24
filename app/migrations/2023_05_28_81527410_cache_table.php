<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class CacheTable implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        $sc = new Schema('cache');

        $sc
        ->integer('id')->auto()->pri()
        ->string('_key_', 191)
        ->text('value')
        ->integer('expires_at')
        ->timestamp('cached_at')->nullable()
		// ...
        ->datetimes();

		$sc->create();
    }

    /**
	* Run undo migration.
    *
    * @return void
    */
    public function down()
    {
        Schema::dropIfExists('cache');
    }
}

