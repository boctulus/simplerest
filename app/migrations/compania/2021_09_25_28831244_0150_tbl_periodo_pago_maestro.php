<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblPeriodoPago implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_periodo_pago (
  pep_intId int(11) NOT NULL AUTO_INCREMENT,
  pep_varNombre varchar(50) NOT NULL,
  pep_dtimFechaCreacion DATETIME NULL DEFAULT NULL,
  pep_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
  est_intIdEstado int(11) NOT NULL DEFAULT 1,
  usu_intIdCreador int(11) NOT NULL,
  usu_intIdActualizador int(11) NOT NULL,
  PRIMARY KEY (pep_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci,
COMMENT = ' * Descripcion: Tabla tbl_periodo_pago 
 * Author: http://www.divergente.net.co");
    }
}

