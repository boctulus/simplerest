<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblBancoConstrainst275 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_banco 
  ADD CONSTRAINT FK_banco_sub_cuenta_contable FOREIGN KEY (sub_intIdCuentaCxC)
    REFERENCES tbl_sub_cuenta_contable(sub_intId);
");
    }
}

