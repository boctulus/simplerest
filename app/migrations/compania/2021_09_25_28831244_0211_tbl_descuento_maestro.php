<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblDescuento implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_descuento (
  des_intId INT(11) NOT NULL AUTO_INCREMENT,
  des_varDescuento VARCHAR(100) NOT NULL,
  des_decDescuento DECIMAL(18, 2) NOT NULL,
  des_dtimFechaCreacion DATETIME NULL DEFAULT NULL,
  des_timFechaActualizacion DATETIME NULL DEFAULT NULL,
  est_intIdEstado INT(11) NOT NULL DEFAULT 1,
  usu_intIdCreador INT(11) NOT NULL,
  usu_intIdActualizador INT(11) NOT NULL,
  PRIMARY KEY (des_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci,
COMMENT = ' * DescripciÃƒÂ³n: Tabla para registrar los diferentes descuentos de los alidos.
 * Author: http://www.divergente.net.co");
    }
}

