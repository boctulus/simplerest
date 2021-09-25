<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblLlaveImpuesto implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_llave_impuesto 
  ADD CONSTRAINT FK_lla_intIdUsuarioActualizador FOREIGN KEY (usu_intIdActualizador)
    REFERENCES tbl_usuario(usu_intId);
");
    }
}

