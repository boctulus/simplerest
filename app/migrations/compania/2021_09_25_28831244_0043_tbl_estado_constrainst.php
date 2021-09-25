<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblEstado implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_estado
  ADD CONSTRAINT FK_est_IdCreador FOREIGN KEY (usu_intIdCreador)
  REFERENCES tbl_usuario (usu_intId);");
    }
}

