<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblMotivoRetiro implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_motivo_retiro (
  mtr_intId int(11) NOT NULL AUTO_INCREMENT,
  mtr_varNombre varchar(50) NOT NULL,
  mtr_varDescripcion varchar(250) NOT NULL,
  mtr_dtimFechaCreacion DATETIME NULL DEFAULT NULL,
  mtr_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
  est_intIdEstado int(11) NOT NULL DEFAULT 1,
  usu_intIdCreador int(11) NOT NULL,
  usu_intIdActualizador int(11) NOT NULL,
  PRIMARY KEY (mtr_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci,
COMMENT = ' * Descripcion: Tabla tbl_motivo_retiro 
 * Author: http://www.divergente.net.co");
    }
}

