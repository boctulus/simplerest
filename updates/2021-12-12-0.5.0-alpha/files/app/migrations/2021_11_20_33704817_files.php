<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class FilesAddPri implements IMigration
{
    function __construct(){
        get_default_connection();
    }

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

