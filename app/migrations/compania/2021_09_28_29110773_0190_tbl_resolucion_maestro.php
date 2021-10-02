<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblResolucionMaestro190 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_resolucion (
  res_intId INT(11) NOT NULL AUTO_INCREMENT,
  res_varResolucion VARCHAR(100) NOT NULL DEFAULT '0',
  res_bolEstado TINYINT(4) NOT NULL,
  res_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
  res_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
  usu_intIdCreador INT(11) NOT NULL,
  usu_intIdActualizador INT(11) NOT NULL,
  PRIMARY KEY (res_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;");
    }
}

