<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;
class Parts implements IMigration
{
    protected $table = 'parts';

    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        $sc = new Schema($this->table);
        $sc
        ->integer('id')->auto()->pri()
        ->varchar('name')
        ->varchar('part_num')      // ajustar luego long
        ->varchar('description')   // ajustar luego long
        ->int('the_order');
		// ...
        $sc->create();
    }

    /**
	* Run undo migration.
    *
    * @return void
    */
    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}