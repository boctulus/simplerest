<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblPeriodoPago implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("INSERT INTO tbl_periodo_pago (pep_varNombre,usu_intIdCreador, usu_intIdActualizador)
  VALUES  ('DECADAL',1,1),
  ('QUINCENAL',1,1),
  ('MENSUAL',1,1)
  ;");
    }
}

