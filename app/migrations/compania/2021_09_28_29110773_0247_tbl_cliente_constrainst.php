<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblClienteConstrainst247 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_cliente 
  ADD CONSTRAINT FK_cli1_dias_pago FOREIGN KEY (dpa_intIdDiasPago)
    REFERENCES tbl_dias_pago(dpa_intId);");
    }
}
