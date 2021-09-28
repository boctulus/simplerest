<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblProveedorMaestro284 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_proveedor (
  prv_intId INT(11) NOT NULL AUTO_INCREMENT,
  pro_intCuentaBancaria VARCHAR(15) NOT NULL,
  prv_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
  prv_dtimFechaActualizacion DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  dpa_intIdDiasPago INT(11) NOT NULL,
  ban_intIdBanco INT(11) NOT NULL,
  ccb_intIdCategoriaCuentaBancaria INT(11) NOT NULL,
  per_intIdPersona INT(11) DEFAULT NULL,
  est_intIdEstado INT(11) DEFAULT 1,
  usu_intIdCreador INT(11) DEFAULT NULL,
  usu_intIdActualizador INT(11) DEFAULT NULL,
  PRIMARY KEY (prv_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;
");
    }
}

