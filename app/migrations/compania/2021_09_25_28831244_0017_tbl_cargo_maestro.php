<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblCargo implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_cargo (
  car_intId int(11) NOT NULL AUTO_INCREMENT,
  car_varNombre varchar(50) NOT NULL,
  car_varDescripcion varchar(100) NOT NULL,
  car_dtimFechaCreacion datetime NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  car_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
  emp_intIdEmpresa int(11) DEFAULT NULL,
  est_intIdEstado int(11) NOT NULL DEFAULT 1,
  usu_intIdCreador int(11) NOT NULL,
  usu_intIdActualizador int(11) NOT NULL,
  PRIMARY KEY (car_intId)
)
ENGINE = INNODB,
AUTO_INCREMENT = 1,
AVG_ROW_LENGTH = 4096,
CHARACTER SET utf8,
COLLATE utf8_general_ci,
COMMENT = ' * DescripciÃƒÂ³n: Tabla para registrar los diferentes Cargos.
 * Author: http://www.divergente.net.co");
    }
}

