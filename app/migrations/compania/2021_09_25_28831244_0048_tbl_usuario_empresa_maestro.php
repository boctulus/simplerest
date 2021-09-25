<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblUsuarioEmpresa implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_usuario_empresa (
  uem_intId int(11) NOT NULL AUTO_INCREMENT,
  uem_dtimFechaCreacion datetime NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  uem_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
  usu_intIdUsuario int(11) NOT NULL,
  emp_intIdempresa int(11) NOT NULL,
  usu_intIdCreador int(11) NOT NULL,
  usu_intIdActualizador int(11) NOT NULL,
  PRIMARY KEY (uem_intId)
)
ENGINE = INNODB,
AUTO_INCREMENT = 1,
AVG_ROW_LENGTH = 1638,
CHARACTER SET utf8,
COLLATE utf8_general_ci;");
    }
}

