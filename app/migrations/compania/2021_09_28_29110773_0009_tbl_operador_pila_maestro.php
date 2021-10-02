<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblOperadorPilaMaestro9 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_operador_pila (
  opp_intId int(11) NOT NULL AUTO_INCREMENT,
  opp_varCodigo varchar(50) NOT NULL,
  opp_varDescripcion varchar(300) NOT NULL,
  opp_dtimFechaCreacion datetime NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  opp_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
  est_intIdEstado int(11) NOT NULL DEFAULT 1,
  usu_intIdCreador int(11) NOT NULL,
  usu_intIdActualizador int(11) NOT NULL,
  PRIMARY KEY (opp_intId)
)
ENGINE = INNODB,
AUTO_INCREMENT = 1,
AVG_ROW_LENGTH = 8192,
CHARACTER SET utf8,
COLLATE utf8_general_ci,
COMMENT = ' * Descripcion: Tabla tbl_operador_pila 
 * Author: http://www.divergente.net.co';");
    }
}

