<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblDepartamentoConstrainst134 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_departamento
ADD CONSTRAINT FK_dep_idPais FOREIGN KEY (pai_intIdPais)
REFERENCES tbl_pais (pai_intId);");
    }
}
