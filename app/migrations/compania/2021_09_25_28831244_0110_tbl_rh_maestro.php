<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblRh implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_rh (
  trh_intId int(11) NOT NULL AUTO_INCREMENT,
  trh_varCodigo varchar(30) NOT NULL,
  trh_varDescripcion varchar(250) NOT NULL,
  trh_dtimFechaCreacion DATETIME NULL DEFAULT NULL,
  trh_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
  est_intIdEstado int(11) DEFAULT NULL,
  usu_intIdCreador int(11) NOT NULL,
  usu_intIdActualizador int(11) NOT NULL,
  PRIMARY KEY (trh_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci,
COMMENT = ' * Descripcion: Tabla tbl_RH 
 * Author: http://www.divergente.net.co");
    }
}

