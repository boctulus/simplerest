<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblTipoPersona implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_tipo_persona (
  tpr_intId INT(11) NOT NULL AUTO_INCREMENT,
  tpr_varNombre VARCHAR(100) NOT NULL,
  tpr_dtimFechaCreacion DATETIME NULL DEFAULT NULL,
  tpr_dtimFechaActualizacion DATETIME DEFAULT '0000-00-00 00:00:00',
  est_intIdEstado INT(11) NOT NULL DEFAULT 1,
  usu_intIdCreador INT(11) NOT NULL,
  usu_intIdActualizador INT(11) NOT NULL,
  PRIMARY KEY (tpr_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;");
    }
}

