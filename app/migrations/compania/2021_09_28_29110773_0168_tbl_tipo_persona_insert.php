<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblTipoPersonaInsert168 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("INSERT INTO tbl_tipo_persona 
(
  tpr_varNombre,usu_intIdCreador,usu_intIdActualizador
)
VALUES 
(
  'NATURAL',1,1
),
(
  'JURIDICA',1,1
);");
    }
}

