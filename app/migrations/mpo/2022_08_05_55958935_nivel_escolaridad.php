<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class NivelEscolaridad implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        DB::setConnection('mpo');
		
		$table = new Schema('el_nivel_escolaridad');
        $table->increments();
        $table->string('nombre', 20)->unique();
        $table->timestamps();
		$table->create();
    }

    public function down(){
        DB::setConnection('mpo');

        Schema::dropIfExists('el_nivel_escolaridad');
    }
}

