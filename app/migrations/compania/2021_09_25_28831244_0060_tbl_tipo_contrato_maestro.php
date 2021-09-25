<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblTipoContrato implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_tipo_contrato (
  tic_intId int(11) NOT NULL AUTO_INCREMENT,
  tic_varNombre varchar(100) NOT NULL,
  tic_varDescripcion longtext NOT NULL,
  tic_varCodigoDian varchar(20) NOT NULL,
  tic_dtimFechaCreacion DATETIME NULL DEFAULT NULL,
  tic_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
  est_intIdEstado int(11) NOT NULL DEFAULT 1,
  usu_intIdCreador int(11) NOT NULL,
  usu_intIdActualizador int(11) NOT NULL,
  PRIMARY KEY (tic_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci,
COMMENT = ' * DescripciÃƒÂ³n: Tabla tbl_tipo_contrato
 * Author: http://www.divergente.net.co");
    }
}

