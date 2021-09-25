<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblProducto implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_producto 
  ADD CONSTRAINT FK_producto_sub_cuenta_contable FOREIGN KEY (sub_intIdCuentaContableCompra)
    REFERENCES tbl_sub_cuenta_contable(sub_intId);


");
    }
}

