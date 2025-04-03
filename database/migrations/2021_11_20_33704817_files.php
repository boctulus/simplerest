<?php

use Boctulus\Simplerest\Core\Interfaces\IMigration;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\Schema;
use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Libs\DB;

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

