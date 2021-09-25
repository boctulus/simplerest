<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblMoneda implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_moneda (
  mon_intId int(11) NOT NULL AUTO_INCREMENT,
  mon_varCodigoMoneda varchar(50) NOT NULL,
  mon_varDescripcion varchar(100) NOT NULL,
  mon_dtimFechaCreacion DATETIME NULL DEFAULT NULL,
  mon_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
  est_intIdEstado int(11) NOT NULL DEFAULT 1,
  usu_intIdCreador int(11) NOT NULL,
  usu_intIdActualizador int(11) NOT NULL,
  PRIMARY KEY (mon_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci,
COMMENT = ' * DescripciÃƒÆ’Ã‚Â³n: Tabla para registrar el tipo de moneda.
 * Author: http://www.divergente.net.co");
    }
}

