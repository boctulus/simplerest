<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblLlaveImpuestoMaestro308 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_llave_impuesto (
  lla_intId INT(11) NOT NULL AUTO_INCREMENT,
  lla_varNombreLLave VARCHAR(50) NOT NULL,
  lla_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
  lla_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
  ret_intIdRetencionCuentacontable INT(11) NOT NULL,
  iva_intIdIvaCuentaContable INT(11) NOT NULL,
  est_intIdEstado INT(11) NOT NULL DEFAULT 1,
  usu_intIdCreador INT(11) NOT NULL DEFAULT 0,
  usu_intIdActualizador INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (lla_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;
");
    }
}

