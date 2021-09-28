<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblLlaveImpuestoConstrainst312 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_llave_impuesto 
  ADD CONSTRAINT FK_lla_intIdRetencionCuenta FOREIGN KEY (ret_intIdRetencionCuentacontable)
    REFERENCES tbl_retencion_cuentacontable(rec_intId);
");
    }
}

