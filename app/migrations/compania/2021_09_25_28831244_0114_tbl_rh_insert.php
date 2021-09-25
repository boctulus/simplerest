<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblRh implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("INSERT INTO tbl_rh
(
   trh_varCodigo, trh_varDescripcion,usu_intIdCreador,usu_intIdActualizador
)
VALUES 
(
  '01', 'O NEGATIVO',1,1
),
(
  '02', 'O POSITIVO',1,1
),
(
  '03', 'A NEGATIVO',1,1
),
(
  '04', 'A POSITIVO',1,1
),
(
  '05', 'B NEGATIVO',1,1
),
(
  '06', 'B POSITIVO',1,1
),
(
  '07', 'AB NEGATIVO',1,1
)
,
(
  '08', 'AB POSITIVO',1,1
);");
    }
}

