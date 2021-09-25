<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblGenero implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_genero (
  gen_intId int(11) NOT NULL AUTO_INCREMENT,
  gen_varGenero varchar(50) NOT NULL,
  gen_dtimFechaCreacion DATETIME NULL DEFAULT NULL,
  gen_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
  est_intIdEstado int(11) NOT NULL DEFAULT 1,
  usu_intIdCreador int(11) NOT NULL,
  usu_intIdActualizador int(11) NOT NULL,
  PRIMARY KEY (gen_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci,
COMMENT = ' * DescripciÃƒÂ³n: Tabla Genero..
 * Author: http://www.divergente.net.co");
    }
}

