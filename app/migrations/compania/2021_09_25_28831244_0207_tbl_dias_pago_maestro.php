<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblDiasPago implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_dias_pago (
  dpa_intId INT(11) NOT NULL AUTO_INCREMENT,
  dpa_intDiasPago INT(11) NOT NULL,
  dpa_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
  dpa_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
  est_intIdEstado INT(11) NOT NULL DEFAULT 1,
  usu_intIdCreador INT(11) NOT NULL,
  usu_intIdActualizador INT(11) NOT NULL,
  PRIMARY KEY (dpa_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci,
COMMENT = ' * DescripciÃƒÂ³n: Tabla para registrar los diferentes dias o plazos de Pagos.
 * Author: http://www.divergente.net.co");
    }
}

