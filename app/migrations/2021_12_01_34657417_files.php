<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class FilesAddDescription implements IMigration
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
        $sc->varchar('description')->nullable();
        $sc->alter();
    }
}
