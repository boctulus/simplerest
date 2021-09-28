<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblBancoMaestro271 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_banco (
  ban_intId INT(11) NOT NULL AUTO_INCREMENT,
  ban_varCodigo VARCHAR(4) NOT NULL,
  ban_varDescripcion VARCHAR(50) NOT NULL,
  ban_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
  ban_dtimFechaActualizacion DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  est_intIdEstado INT(11) NOT NULL DEFAULT 1,
  usu_intIdCreador INT(11) NOT NULL,
  usu_intIdActualizador INT(11) NOT NULL,
  sub_intIdCuentaCxC INT(11) NOT NULL,
  PRIMARY KEY (ban_intId, ban_varCodigo)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;");
    }
}

