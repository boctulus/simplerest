<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblBancoConstrainst272 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_banco 
  ADD CONSTRAINT FK_ban_IdActualizador FOREIGN KEY (usu_intIdActualizador)
    REFERENCES tbl_usuario(usu_intId);");
    }
}

