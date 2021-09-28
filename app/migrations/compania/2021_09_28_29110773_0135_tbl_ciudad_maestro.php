<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblCiudadMaestro135 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_ciudad (
  ciu_intId int(11) NOT NULL AUTO_INCREMENT,
  ciu_varCodigo varchar(5) NOT NULL,
  ciu_varCiudad varchar(100) NOT NULL,
  ciu_varIndicativoTelefono varchar(3) NOT NULL,
  ciu_dtimFechaCreacion datetime NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  ciu_dtimFechaActualizacion datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  est_intIdEstado int(11) NOT NULL DEFAULT 1,
  pai_intIdPais int(11) NOT NULL,
  dep_intIdDepartamento int(11) NOT NULL,
  usu_intIdCreador int(11) NOT NULL,
  usu_intIdActualizador int(11) NOT NULL,
  PRIMARY KEY (ciu_intId, ciu_varCodigo),
  INDEX ciu_intId (ciu_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci,
COMMENT = ' * DescripciÃƒÂ³n: Tabla para registrar los diferentes Ciudades.
 * Author: http://www.divergente.net.co");
    }
}

