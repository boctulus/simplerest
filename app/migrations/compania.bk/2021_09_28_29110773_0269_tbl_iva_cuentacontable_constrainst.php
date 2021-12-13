<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblIvaCuentacontableConstrainst269 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_iva_cuentacontable 
  ADD CONSTRAINT FK_ivc_intIdIva FOREIGN KEY (ivc_intIdIva)
    REFERENCES tbl_iva(iva_intId);");
    }
}

