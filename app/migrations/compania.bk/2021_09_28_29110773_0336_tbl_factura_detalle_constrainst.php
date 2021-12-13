<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblFacturaDetalleConstrainst336 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_factura_detalle 
  ADD CONSTRAINT FK_fde_idCreador FOREIGN KEY (usu_intIdCreador)
    REFERENCES tbl_usuario(usu_intId);
");
    }
}

