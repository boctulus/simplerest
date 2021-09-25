<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblCategoriaDocumento implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_categoria_documento (
  cdo_intId int(11) NOT NULL AUTO_INCREMENT,
  cdo_varCategoriaDocumento varchar(50) NOT NULL,
  cdo_varSiglas varchar(3) NOT NULL,
  cdo_dtimFechaCreacion DATETIME NULL DEFAULT NULL,
  cdo_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
  est_intIdEstado int(11) NOT NULL DEFAULT 1,
  usu_intIdCreador int(11) DEFAULT NULL,
  usu_intIdActualizador int(11) DEFAULT NULL,
  PRIMARY KEY (cdo_intId)
)
ENGINE = INNODB,
AUTO_INCREMENT = 1,
CHARACTER SET utf8,
COLLATE utf8_general_ci,
COMMENT = ' * DescripciÃƒÂ³n: 
 * Author: http://www.divergente.net.co");
    }
}

