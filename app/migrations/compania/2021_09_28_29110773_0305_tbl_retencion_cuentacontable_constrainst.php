<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblRetencionCuentacontableConstrainst305 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_retencion_cuentacontable 
  ADD CONSTRAINT FK_rec_intIdCreador FOREIGN KEY (usu_intIdCreador)
    REFERENCES tbl_usuario(usu_intId);

");
    }
}

