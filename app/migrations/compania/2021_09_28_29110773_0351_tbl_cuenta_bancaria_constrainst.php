<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblCuentaBancariaConstrainst351 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_cuenta_bancaria 
  ADD CONSTRAINT FK_cba_IdEstado FOREIGN KEY (est_intIdEstado_cba)
    REFERENCES tbl_estado(est_intId);

");
    }
}

