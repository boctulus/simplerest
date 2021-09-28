<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblSubCuentaContableMaestro75 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_sub_cuenta_contable (
  sub_intId int(11) NOT NULL AUTO_INCREMENT,
  sub_varCodigoCuenta varchar(20) NOT NULL,
  sub_varNombreCuenta varchar(50) NOT NULL,
  sub_varConceptoMedioMagnetico varchar(50) NOT NULL,
  sub_varEquivalenciaFisica varchar(50) NOT NULL,
  sub_tinManejaTercero tinyint(4) NOT NULL,
  sub_tinManejaCentroCostos tinyint(4) NOT NULL,
  sub_tinManejaBase tinyint(4) NOT NULL,
  sub_intPorcentajeBase int(11) DEFAULT 0,
  sub_decMontobase decimal(10, 2) DEFAULT 0.00,
  sub_tinCuentaBalance tinyint(4) NOT NULL DEFAULT 0,
  sub_tinCuentaResultado tinyint(4) NOT NULL DEFAULT 0,
  sub_dtimFechaCreacion datetime NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  sub_dtimFechaActualizacion datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  mon_intIdMoneda int(11) NOT NULL,
  ccc_intIdCategoriaCuentaContable int(11) NOT NULL,
  cue_intIdCuentaContable int(11) NOT NULL,
  nat_intIdNaturalezaCuentaContable int(11) NOT NULL,
  est_intIdEstado int(11) NOT NULL DEFAULT 1,
  usu_intIdCreador int(11) NOT NULL,
  usu_intIdActualizador int(11) NOT NULL,
  PRIMARY KEY (sub_intId, sub_varCodigoCuenta)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;");
    }
}

