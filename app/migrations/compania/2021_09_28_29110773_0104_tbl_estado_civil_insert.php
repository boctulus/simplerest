<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblEstadoCivilInsert104 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("INSERT into tbl_estado_civil 
(
   esc_varNombre, usu_intIdCreador, usu_intIdActualizador
)
VALUES 
(
  'Soltero', 1,1
),
(
  'Casado', 1,1
),
(
  'Union libre', 1,1
);");
    }
}

