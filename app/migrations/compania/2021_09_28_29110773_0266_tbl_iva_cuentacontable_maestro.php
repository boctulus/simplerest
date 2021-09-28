<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblIvaCuentacontableMaestro266 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_iva_cuentacontable (
  ivc_intId INT(11) NOT NULL AUTO_INCREMENT,
  ivc_intIdIva INT(11) NOT NULL,
  ivc_intIdCuentaContable INT(11) NOT NULL,
  ivc_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
  ivc_dtimFechaActualizacion DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  usu_intIdCreador INT(11) NOT NULL,
  usu_intIdActualizador INT(11) NOT NULL,
  PRIMARY KEY (ivc_intId, ivc_intIdIva, ivc_intIdCuentaContable)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;");
    }
}

