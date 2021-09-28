<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblCategoriaDocumentoInsert23 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("INSERT INTO tbl_categoria_documento 
(
  cdo_varCategoriaDocumento, cdo_varSiglas, usu_intIdCreador, usu_intIdActualizador
)
VALUES 
(
   'NA', 'NO', 1, 1
);");
    }
}

