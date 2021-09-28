<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblCargoInsert20 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("INSERT INTO tbl_cargo 
(
   car_varNombre, car_varDescripcion,  emp_intIdEmpresa,  usu_intIdCreador, usu_intIdActualizador
)
VALUES
(
  'NA', 'NO APLICA', 1, 1, 1
);");
    }
}

