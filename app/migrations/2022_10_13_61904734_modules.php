<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class Modules implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        $sc = new Schema('modules');

        $sc
        ->id()->auto()
		->varchar('name')->unique()
        ->varchar('description', 240)->nullable()
        ->int('role_id')->index();

        $sc
        ->fromField('role_id')->toField('id')->toTable('roles');
    
        $sc->create();		
    }

    function down(){
        Schema::dropIfExists('modules');
    }
}

