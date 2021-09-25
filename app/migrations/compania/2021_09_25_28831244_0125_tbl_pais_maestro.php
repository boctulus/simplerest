<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblPais implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_pais (
  pai_intId int(11) NOT NULL AUTO_INCREMENT,
  pai_varCodigo varchar(4) NOT NULL,
  pai_varPais varchar(100) NOT NULL,
  pai_varCodigoPaisCelular varchar(3) NOT NULL,
  pai_dtimFechaCreacion datetime NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  pai_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
  est_intIdEstado int(11) NOT NULL DEFAULT 1,
  pai_intIdMoneda int(11) NOT NULL,
  usu_intIdCreador int(11) NOT NULL,
  usu_intIdActualizador int(11) NOT NULL,
  PRIMARY KEY (pai_intId, pai_varCodigo),
  INDEX pai_intId (pai_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci,
COMMENT = ' * DescripciÃƒÂ³n: Tabla para registrar los Diferentes Paises.
 * Author: http://www.divergente.net.co");
    }
}

