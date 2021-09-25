<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblPersona implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_persona 
  ADD CONSTRAINT FK_per_idCiudad FOREIGN KEY (ciu_intIdCiudad)
    REFERENCES tbl_ciudad(ciu_intId);
");
    }
}

