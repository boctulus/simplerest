<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblUnidadmedida implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_unidadmedida (
  unm_intId INT(11) NOT NULL AUTO_INCREMENT,
  unm_varUnidadMedida VARCHAR(50) NOT NULL,
  unm_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
  unm_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
  est_intIdEstado INT(11) NOT NULL DEFAULT 1,
  usu_intIdCreador INT(11) NOT NULL,
  usu_intIdActualizador INT(11) NOT NULL,
  PRIMARY KEY (unm_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;");
    }
}

