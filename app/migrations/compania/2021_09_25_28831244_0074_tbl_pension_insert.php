<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblPension implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("INSERT INTO tbl_pension 
(
  pen_varCodigo, pen_varNombre, usu_intIdCreador, usu_intIdActualizador
)
VALUES 
(
  'NA', 'NO APLICA',1,1
); 
");
    }
}

