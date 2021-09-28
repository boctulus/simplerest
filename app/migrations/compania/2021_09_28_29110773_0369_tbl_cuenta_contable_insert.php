<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblCuentaContableInsert369 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("INSERT INTO tbl_cuenta_contable 
(
   cue_varNumeroCuenta, cue_varNombreCuenta, cue_tinCuentaBalance, cue_tinCuentaResultado
  , ccc_intIdCategoriaCuentaContable, gru_intId, usu_intIdCreador, usu_intIdActualizador
)
  VALUES 
(
  '0000', 'SIN TERCERO', 0, 0
  ,0, 0, 1, 1
);");
    }
}

