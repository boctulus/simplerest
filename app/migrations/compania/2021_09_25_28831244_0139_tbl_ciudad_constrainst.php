<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblCiudad implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_ciudad
ADD CONSTRAINT FK_ciu_idDepartamento FOREIGN KEY (dep_intIdDepartamento)
REFERENCES tbl_departamento (dep_intId);");
    }
}

