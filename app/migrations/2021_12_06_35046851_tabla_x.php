<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TablaX implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        $sc = new Schema('tabla_x');
		$sc->renameTableTo('nuevo_nombre_tabla');
		$sc->dropColumn('campo1');
		$sc->dropColumn('campo3');
		$sc->dropColumn('campo18');
		$sc->dropColumn('campo30');
		$sc->renameColumn('campo2', 'campo2b');
		$sc->field('campo4')->nullable();
		$sc->field('campo7')->nullable();
		$sc->field('campo15')->nullable();
		$sc->field('campo8')->dropNullable();
		$sc->field('campo20')->dropNullable();
		$sc->field('campo21')->dropNullable();
		$sc->field('campo9')->primary();
		$sc->field('campo10')->primary();
		$sc->field('campo9')->addAuto();
		$sc->field('campo5')->zeroFill();
		$sc->unique('campo2','campo3','campo5');
		$sc->dropUnique('campo8');
		$sc->alter();
		
    }
}

