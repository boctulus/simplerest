<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblRol implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE IF NOT EXISTS tbl_rol (
  rol_intId int(11) NOT NULL AUTO_INCREMENT,
  rol_varNombre varchar(50) NOT NULL,
  rol_varDescripcion varchar(100) NOT NULL,
  rol_dtimFechaCreacion datetime NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  rol_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
  est_intIdEstado_rol int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (rol_intId)
)
ENGINE = INNODB,
AUTO_INCREMENT = 1,
AVG_ROW_LENGTH = 32768,
CHARACTER SET utf8,
COLLATE utf8_general_ci,
COMMENT = ' * DescripciÃƒÂ³n: 
 * Author: http://www.divergente.net.co");
    }
}

