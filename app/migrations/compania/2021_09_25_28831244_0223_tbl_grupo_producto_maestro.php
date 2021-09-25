<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblGrupoProducto implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_grupo_producto (
  grp_intId INT(11) NOT NULL AUTO_INCREMENT,
  grp_varSiglaGrupoProducto VARCHAR(50) NOT NULL,
  grp_varDescripcionGrupo VARCHAR(50) NOT NULL,
  grp_dtimFechaCreacion DATETIME NULL DEFAULT NULL,
  grp_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
  grp_intConsecutivoGrupoProducto INT(11) NOT NULL DEFAULT 0,
  est_intIdEstado INT(11) NOT NULL DEFAULT 1,
  cap_intIdCategoriaProducto INT(11) DEFAULT NULL,
  usu_intIdCreador INT(11) NOT NULL,
  usu_intIdActualizador INT(11) NOT NULL,
  PRIMARY KEY (grp_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci,
COMMENT = '* DescripciÃƒÂ³n: 
 * Author: http://www.divergente.net.co");
    }
}

