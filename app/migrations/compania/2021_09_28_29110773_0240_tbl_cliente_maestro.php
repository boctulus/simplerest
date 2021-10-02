<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblClienteMaestro240 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_cliente (
  cli_intId INT(11) NOT NULL AUTO_INCREMENT,
  cli_intDiasGracia INT(11) NOT NULL,
  cli_decCupoCredito DECIMAL(18, 2) NOT NULL,
  cli_bolBloqueadoMora TINYINT(1) NOT NULL,
  cli_datFechaBloqueado DATE NOT NULL,
  cli_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
  cli_dtimFechaActualizacion DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  est_intIdEstado INT(11) NOT NULL DEFAULT 1,
  dpa_intIdDiasPago INT(11) NOT NULL,
  des_intIdDescuento INT(11) NOT NULL,
  ali_intIdPersona INT(11) NOT NULL,
  usu_intIdCreador INT(11) DEFAULT NULL,
  usu_intIdActualizador INT(11) DEFAULT NULL,
  PRIMARY KEY (cli_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;");
    }
}

