<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblIvaMaestro258 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_iva (
  iva_intId INT(11) NOT NULL AUTO_INCREMENT,
  iva_varIVA VARCHAR(50) NOT NULL,
  iva_intTope INT(11) NOT NULL,
  iva_decPorcentaje DECIMAL(18, 2) NOT NULL,
  iva_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
  iva_dtimFechaActualizacion DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  est_intIdEstado INT(11) NOT NULL DEFAULT 1,
  usu_intIdCreador INT(11) NOT NULL,
  usu_intIdActualizador INT(11) NOT NULL,
  sub_intIdCuentaContable INT(11) NOT NULL,
  PRIMARY KEY (iva_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;");
    }
}

