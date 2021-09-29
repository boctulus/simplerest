<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblConceptoMaestro155 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_concepto (
  cct_intId int(11) NOT NULL AUTO_INCREMENT,
  cct_varNombre varchar(50) NOT NULL,
  cct_varDescripcion varchar(250) NOT NULL,
  cct_dtimFechaCreacion datetime NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  cct_dtimFechaActualizacion datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  est_intIdEstado int(11) NOT NULL DEFAULT 1,
  usu_intIdCreador int(11) NOT NULL,
  usu_intIdActualizador int(11) NOT NULL,
  PRIMARY KEY (cct_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci,
COMMENT = ' * Descripcion: Tabla tbl_concepto 
 * Author: http://www.divergente.net.co';");
    }
}

