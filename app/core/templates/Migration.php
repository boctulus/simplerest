<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class __NAME__ implements IMigration
{
    protected $table = '__TB_NAME__';

    function __construct(){
        ### CONSTRUCTOR
    }

    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        ### UP

        $sc
        // ..
        ->alter();
    }

    /**
	* Run undo migration.
    *
    * @return void
    */
    public function down()
    {
        ### DOWN

        $sc = new Schema($this->table);

        $sc
        // ..
        ->alter();
    }
}

