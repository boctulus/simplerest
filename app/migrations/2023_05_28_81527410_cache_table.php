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
        $sc->string('key')->primary(); // no esta agregando el primary !!!
        $sc->text('value');
        $sc->timestamp('cached_at')->nullable();
        $sc->integer('expiration_time');
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

