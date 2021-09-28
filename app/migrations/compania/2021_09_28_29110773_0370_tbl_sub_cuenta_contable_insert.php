<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblSubCuentaContableInsert370 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("INSERT INTO tbl_sub_cuenta_contable 
(
   sub_varCodigoCuenta, sub_varNombreCuenta, sub_varConceptoMedioMagnetico, sub_varEquivalenciaFisica, sub_tinManejaTercero, sub_tinManejaCentroCostos
  , sub_tinManejaBase, sub_intPorcentajeBase, sub_decMontobase, sub_tinCuentaBalance, sub_tinCuentaResultado  , mon_intIdMoneda
  , ccc_intIdCategoriaCuentaContable, cue_intIdCuentaContable, nat_intIdNaturalezaCuentaContable
  , usu_intIdCreador, usu_intIdActualizador)
  VALUES 
  (
    '0000000000', 'SIN TERCERO', 'NA', 'NA', 0, 0, 0
    , 0, 0, 0, 0, 1
    , 1,1, 1
    , 1, 1
  );");
    }
}

