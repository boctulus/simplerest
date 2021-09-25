<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblCuentaBancaria implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_cuenta_bancaria 
  ADD CONSTRAINT FK_cba_IdBanco FOREIGN KEY (ban_intIdBanco)
    REFERENCES tbl_banco(ban_intId);

");
    }
}

