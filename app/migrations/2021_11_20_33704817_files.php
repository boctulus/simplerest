<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class Files implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        $sc = new Schema('files');

        $sc->varchar('uuid')->primary();
        $sc->alter();
    }

    public function down(){
        $sc = new Schema('files');

        $sc->dropPrimary('uuid')
        ->alter();
    }
}

