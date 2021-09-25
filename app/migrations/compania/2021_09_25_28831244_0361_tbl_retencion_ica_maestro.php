<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblRetencionIca implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_retencion_ica (
  ric_intId INT(11) NOT NULL AUTO_INCREMENT,
  ric_varReteIca VARCHAR(50) NOT NULL,
  ric_intTope INT(11) NOT NULL,
  ric_intPorcentaje DECIMAL(10, 2) NOT NULL,
  ric_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
  ric_dtimFechaActualizacion DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  est_intIdCidEstado INT(11) NOT NULL DEFAULT 1,
  sub_intIdSubCuentaContable INT(11) DEFAULT NULL,
  usu_intIdCreador INT(11) NOT NULL,
  usu_intIdActualizador INT(11) NOT NULL,
  PRIMARY KEY (ric_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;");
    }
}

