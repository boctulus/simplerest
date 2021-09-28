<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblLlaveImpuestoConstrainst310 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_llave_impuesto 
  ADD CONSTRAINT FK_est_intIdEstado FOREIGN KEY (est_intIdEstado)
    REFERENCES tbl_estado(est_intId);
");
    }
}

