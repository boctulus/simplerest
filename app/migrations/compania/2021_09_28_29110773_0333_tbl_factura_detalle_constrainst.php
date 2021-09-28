<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblFacturaDetalleConstrainst333 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_factura_detalle 
  ADD CONSTRAINT FK_fde_IdBodega FOREIGN KEY (fde_intIdBodega)
    REFERENCES tbl_bodega(bod_intId);
");
    }
}

