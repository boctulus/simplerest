<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblRetencionIcaConstrainst366 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_retencion_ica 
  ADD CONSTRAINT FK_ric_idSubCuentaContable FOREIGN KEY (sub_intIdSubCuentaContable)
    REFERENCES tbl_sub_cuenta_contable(sub_intId);");
    }
}

