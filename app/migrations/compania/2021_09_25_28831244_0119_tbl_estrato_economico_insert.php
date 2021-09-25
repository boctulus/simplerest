<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblEstratoEconomico implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("INSERT INTO tbl_estrato_economico (tec_varCodigo, tec_varDescripcion,  usu_intIdCreador,
  usu_intIdActualizador )
  VALUES ('UNO', 'BAJO-BAJO', 1,1),
  ('DOS', 'BAJO', 1,1),
  ('TRES', 'MEDIO-BAJO', 1,1),
  ('CUATRO', 'MEDIO', 1,1),
  ('CINCO', 'MEDIO-ALTO', 1,1),
  ('SEIS', 'ALTO', 1,1);");
    }
}

