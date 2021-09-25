<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblRetencion implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_retencion (
  ret_intId INT(11) NOT NULL AUTO_INCREMENT,
  ret_varRetencion VARCHAR(50) NOT NULL,
  ret_intTope INT(11) NOT NULL,
  ret_decPorcentaje DECIMAL(10, 2) NOT NULL,
  ret_bolEstado TINYINT(4) NOT NULL DEFAULT 1,
  ret_varCodigoSiesa VARCHAR(10) NOT NULL,
  ret_varCuentaArbo VARCHAR(50) NOT NULL,
  ret_dtimFechaCreacion DATETIME NULL DEFAULT NULL,
  ret_dtimFechaActualizacion DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  usu_intIdCreador INT(11) NOT NULL,
  usu_intIdActualizador INT(11) NOT NULL,
  sub_intIdCuentaContable INT(11) DEFAULT NULL,
  PRIMARY KEY (ret_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;
");
    }
}

