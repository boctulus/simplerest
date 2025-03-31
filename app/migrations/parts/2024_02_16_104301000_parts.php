<?php

use Boctulus\Simplerest\Core\Interfaces\IMigration;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\Schema;
use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Libs\DB;
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