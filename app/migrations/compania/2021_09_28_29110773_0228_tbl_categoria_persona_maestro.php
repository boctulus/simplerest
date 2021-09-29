<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblCategoriaPersonaMaestro228 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_categoria_persona (
  cap_intId INT(11) NOT NULL AUTO_INCREMENT,
  cap_varCategoriaPersona VARCHAR(100) NOT NULL,
  cap_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
  cap_dtimFechaActualizacion DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  est_intIdEstado INT(11) NOT NULL DEFAULT 1,
  usu_intIdCreador INT(11) NOT NULL,
  usu_intIdActualizador INT(11) NOT NULL,
  PRIMARY KEY (cap_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci,
COMMENT = ' * Descripcion: Tabla para registrar las diferentes categorias de los aliados.
 * Author: http://www.divergente.net.co';");
    }
}

