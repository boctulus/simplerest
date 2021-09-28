<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblBancoConstrainst274 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_banco 
  ADD CONSTRAINT FK_ban_IdEstado FOREIGN KEY (est_intIdEstado)
    REFERENCES tbl_estado(est_intId);
");
    }
}

