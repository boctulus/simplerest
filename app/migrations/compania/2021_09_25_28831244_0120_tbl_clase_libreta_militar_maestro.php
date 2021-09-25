<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblClaseLibretaMilitar implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_clase_libreta_militar (
  clm_intId int(11) NOT NULL AUTO_INCREMENT,
  clm_varNombre varchar(50) NOT NULL,
  clm_dtimFechaCreacion datetime NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  clm_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
  est_intIdEstado int(11) NOT NULL DEFAULT 1,
  usu_intIdCreador int(11) NOT NULL,
  usu_intIdActualizador int(11) NOT NULL,
  PRIMARY KEY (clm_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci,
COMMENT = ' * Descripcion: Tabla tbl_clase_libreta_militar 
 * Author: http://www.divergente.net.co");
    }
}

