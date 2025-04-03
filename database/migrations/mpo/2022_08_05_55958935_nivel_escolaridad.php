<?php

use Boctulus\Simplerest\Core\Interfaces\IMigration;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\Schema;
use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Libs\DB;

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

