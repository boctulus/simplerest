<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblIvaCuentacontable implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_iva_cuentacontable 
  ADD CONSTRAINT FK_iva_cuentacontable_sub_cuenta_contable FOREIGN KEY (ivc_intIdCuentaContable)
    REFERENCES tbl_sub_cuenta_contable(sub_intId);");
    }
}

