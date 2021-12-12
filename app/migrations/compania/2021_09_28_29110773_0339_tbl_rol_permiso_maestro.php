<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblRolPermisoMaestro339 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_rol_permiso (
  rpe_intId INT(11) NOT NULL AUTO_INCREMENT,
  rpe_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
  rpe_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
  est_intIdEstado INT(11) NOT NULL DEFAULT 1,
  rol_intIdRol INT(11) NOT NULL,
  per_intIdPermiso INT(11) NOT NULL,
  usu_intIdCreador INT(11) NOT NULL,
  usu_intIdActualizador INT(11) NOT NULL,
  PRIMARY KEY (rpe_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci,
COMMENT = ' * DescripciÃ³n: 
 * Author: http://www.divergente.net.co';");
    }
}

