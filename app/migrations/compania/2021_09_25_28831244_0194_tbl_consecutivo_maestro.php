<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblConsecutivo implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_consecutivo (
  cse_intId INT(11) NOT NULL AUTO_INCREMENT,
  cse_intConsecutivo INT(11) NOT NULL DEFAULT 0,
  cse_varPrefijo VARCHAR(4) DEFAULT NULL,
  cse_intDesde INT(11) NOT NULL,
  cse_intHasta INT(11) NOT NULL,
  cse_dateFechaInicial DATE NOT NULL,
  cse_dateFechaFinal DATE NOT NULL,
  cse_varVigencia VARCHAR(2) NOT NULL,
  cse_bolEstado TINYINT(1) NOT NULL,
  cse_dtimFechaCreacion DATETIME NULL DEFAULT NULL,
  cse_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
  doc_intIdDocumento INT(11) NOT NULL,
  res_intIdResolucion INT(11) NOT NULL,
  usu_intIdCreador INT(11) NOT NULL,
  usu_intIdActualizador INT(11) NOT NULL,
  PRIMARY KEY (cse_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;");
    }
}

