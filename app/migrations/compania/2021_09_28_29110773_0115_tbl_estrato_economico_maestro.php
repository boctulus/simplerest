<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblEstratoEconomicoMaestro115 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_estrato_economico (
  tec_intId int(11) NOT NULL AUTO_INCREMENT,
  tec_varCodigo varchar(20) NOT NULL,
  tec_varDescripcion varchar(250) NOT NULL,
  tec_dtimFechaCreacion datetime NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  tec_dtimFechaActualizacion datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  est_intIdestado int(11) DEFAULT NULL,
  usu_intIdCreador int(11) NOT NULL,
  usu_intIdActualizador int(11) NOT NULL,
  PRIMARY KEY (tec_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci,
COMMENT = ' * Descripcion: Tabla tbl_estrato_economico 
 * Author: http://www.divergente.net.co");
    }
}

