<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblTipoCuentaBancariaInsert99 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("INSERT INTO tbl_tipo_cuenta_bancaria
(
  tcb_varDescripcion,usu_intIdCreador,usu_intIdActualizador
)
VALUES 
(
  'CORRIENTE',1,1
),
(
  'AHORROS',1,1
),
(
  'NEQUI',1,1
);
");
    }
}

