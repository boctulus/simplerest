<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblEps implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_eps (
  eps_intId int(11) NOT NULL AUTO_INCREMENT,
  eps_varCodigo varchar(100) NOT NULL,
  eps_varNombre varchar(100) NOT NULL,
  eps_dtimFechaCreacion DATETIME NULL DEFAULT NULL,
  eps_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
  est_intIdEstado int(11) NOT NULL DEFAULT 1,
  usu_intIdCreador int(11) NOT NULL,
  usu_intIdActualizador int(11) NOT NULL,
  PRIMARY KEY (eps_intId)
)
ENGINE = INNODB,
AUTO_INCREMENT = 41,
AVG_ROW_LENGTH = 1489,
CHARACTER SET utf8,
COLLATE utf8_general_ci,
COMMENT = ' * DescripciÃƒÂ³n: Tabla tbl_eps
 * Author: http://www.divergente.net.co");
    }
}

