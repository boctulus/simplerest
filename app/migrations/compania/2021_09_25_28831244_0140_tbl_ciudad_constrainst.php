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
ADD CONSTRAINT FK_ciu_idPais FOREIGN KEY (pai_intIdPais)
REFERENCES tbl_pais (pai_intId);");
    }
}

