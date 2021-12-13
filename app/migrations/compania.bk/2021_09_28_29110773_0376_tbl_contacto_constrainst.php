<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblContactoConstrainst376 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_contacto 
  ADD CONSTRAINT FK_con_idCiudad FOREIGN KEY (ciu_intIdCiudad)
    REFERENCES tbl_ciudad(ciu_intId);");
    }
}

