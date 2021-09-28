<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblFacturaDetalleConstrainst334 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_factura_detalle 
  ADD CONSTRAINT FK_fde_IdProducto FOREIGN KEY (pro_intIdProducto)
    REFERENCES tbl_producto(pro_intId);
");
    }
}

