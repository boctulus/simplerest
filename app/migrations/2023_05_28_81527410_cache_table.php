<?php

use Boctulus\Simplerest\Core\Interfaces\IMigration;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\Schema;
use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Libs\DB;

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

