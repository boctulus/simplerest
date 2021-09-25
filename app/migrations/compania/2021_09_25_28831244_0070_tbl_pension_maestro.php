<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblPension implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_pension (
  pen_intId int(11) NOT NULL AUTO_INCREMENT,
  pen_varCodigo varchar(100) NOT NULL,
  pen_varNombre varchar(100) NOT NULL,
  pen_dtimFechaCreacion datetime NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  pen_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
  est_intIdEstado int(11) NOT NULL DEFAULT 1,
  usu_intIdCreador int(11) NOT NULL,
  usu_intIdActualizador int(11) NOT NULL,
  PRIMARY KEY (pen_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci,
COMMENT = ' * DescripciÃƒÂ³n: Tabla tbl_pension
 * Author: http://www.divergente.net.co");
    }
}

