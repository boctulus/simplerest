<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblEstudiosInsert109 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("INSERT into tbl_estudios 
(
   esd_varNombre, usu_intIdCreador, usu_intIdActualizador
)
VALUES 
(
  'ANALFABETA', 1,1
),
(
  'PRIMARIA', 1,1
),
(
  'SECUNDARIA', 1,1
),
(
  'TECNICO', 1,1
),
(
  'TECNOLOGO', 1,1
),
(
  'PROFESIONAL', 1,1
),
(
  'PREGRADO', 1,1
),
(
  'POSTGRADO', 1,1
);");
    }
}

