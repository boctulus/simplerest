<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblClienteInformacionTributaria implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_cliente_informacion_tributaria 
  ADD CONSTRAINT FK_tic1_IdActualizador FOREIGN KEY (usu_intIdActualizador)
    REFERENCES tbl_usuario(usu_intId);
");
    }
}

