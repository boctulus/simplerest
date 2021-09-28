<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblArlInsert8 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("INSERT INTO tbl_arl 
(
  arl_varCodigo, arl_varNombre, usu_intIdCreador, usu_intIdActualizador
)
VALUES 
(
  'NA', 'APLICA',  1, 1
);");
    }
}

