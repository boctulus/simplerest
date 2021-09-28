<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblIvaConstrainst265 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_iva 
  ADD CONSTRAINT FK_iva_sub_cuenta_contable1 FOREIGN KEY (sub_intIdCuentaContable)
    REFERENCES tbl_sub_cuenta_contable(sub_intId);");
    }
}

