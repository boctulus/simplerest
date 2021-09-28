<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblLlaveImpuestoConstrainst311 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_llave_impuesto 
  ADD CONSTRAINT FK_lla_intIdIvaCuenta FOREIGN KEY (iva_intIdIvaCuentaContable)
    REFERENCES tbl_iva_cuentacontable(ivc_intId);
");
    }
}

