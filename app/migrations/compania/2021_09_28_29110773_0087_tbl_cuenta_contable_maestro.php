<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblCuentaContableMaestro87 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_cuenta_contable (
  cue_intId int(11) NOT NULL AUTO_INCREMENT,
  cue_varNumeroCuenta varchar(4) NOT NULL,
  cue_varNombreCuenta varchar(50) NOT NULL,
  cue_tinCuentaBalance tinyint(4) NOT NULL DEFAULT 0,
  cue_tinCuentaResultado tinyint(4) NOT NULL DEFAULT 0,
  cue_dtimFechaCreacion datetime NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  cue_dtimFechaActualizacion datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  ccc_intIdCategoriaCuentaContable int(11) NOT NULL DEFAULT 0,
  est_intIdEstado int(11) NOT NULL DEFAULT 1,
  gru_intId int(11) NOT NULL DEFAULT 0,
  usu_intIdCreador int(11) NOT NULL DEFAULT 0,
  usu_intIdActualizador int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (cue_intId, cue_varNumeroCuenta)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;");
    }
}

