<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblProveedor implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_proveedor 
  ADD CONSTRAINT FK_prv_idEstadoPersona FOREIGN KEY (est_intIdEstado)
    REFERENCES tbl_estado(est_intId) ON DELETE NO ACTION ON UPDATE NO ACTION;
");
    }
}

