<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblDepartamentoMaestro130 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_departamento (
  dep_intId int(11) NOT NULL AUTO_INCREMENT,
  dep_varCodigoDepartamento varchar(50) NOT NULL,
  dep_varDepartamento varchar(100) NOT NULL,
  dep_dtimFechaCreacion datetime NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  dep_dtimFechaActualizacion datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  est_intIdEstado int(11) NOT NULL DEFAULT 1,
  pai_intIdPais int(11) NOT NULL,
  usu_intIdCreador int(11) NOT NULL,
  usu_intIdActualizador int(11) NOT NULL,
  PRIMARY KEY (dep_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci,
COMMENT = ' * DescripciÃƒÂ³n: Tabla para registrar los Departamentos.
 * Author: http://www.divergente.net.co");
    }
}

