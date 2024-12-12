<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\DB;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;

class PowerConsumption implements IMigration
{
    protected $table = 'consumption';

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

        $sc = new Schema($this->table);

        $sc
        ->integer('id')->auto()->pri()
        ->int('reading')
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
        ### DOWN

        Schema::dropIfExists($this->table);
    }
}


