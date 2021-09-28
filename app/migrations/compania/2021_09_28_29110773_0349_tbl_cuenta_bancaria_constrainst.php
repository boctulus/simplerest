<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblCuentaBancariaConstrainst349 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_cuenta_bancaria 
  ADD CONSTRAINT FK_cba_IdCategoriaCuentaBancaria FOREIGN KEY (ccb_intIdCategoriaCuentaBancaria)
    REFERENCES tbl_categoria_cuenta_bancaria(ccb_intId);

");
    }
}

