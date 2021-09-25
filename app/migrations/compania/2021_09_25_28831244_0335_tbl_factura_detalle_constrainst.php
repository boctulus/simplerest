<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblFacturaDetalle implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_factura_detalle 
  ADD CONSTRAINT FK_fde_idActualizador FOREIGN KEY (usu_intIdActualizador)
    REFERENCES tbl_usuario(usu_intId);
");
    }
}

