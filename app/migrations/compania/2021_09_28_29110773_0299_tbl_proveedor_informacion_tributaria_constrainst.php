<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblProveedorInformacionTributariaConstrainst299 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_proveedor_informacion_tributaria 
  ADD CONSTRAINT FK_tip_IdEstado FOREIGN KEY (est_intIdEstado)
    REFERENCES tbl_estado(est_intId);
");
    }
}

