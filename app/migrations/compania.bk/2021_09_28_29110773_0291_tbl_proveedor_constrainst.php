<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblProveedorConstrainst291 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_proveedor 
  ADD CONSTRAINT FK_proveedor_dias_pago FOREIGN KEY (dpa_intIdDiasPago)
    REFERENCES tbl_dias_pago(dpa_intId);
");
    }
}

