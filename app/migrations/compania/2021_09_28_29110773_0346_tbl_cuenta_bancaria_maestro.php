<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblCuentaBancariaMaestro346 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_cuenta_bancaria (
  cba_intId INT(11) NOT NULL AUTO_INCREMENT,
  cba_varDescripcion VARCHAR(100) NOT NULL,
  cba_varNumeroCuenta VARCHAR(11) NOT NULL,
  cba_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
  cba_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
  est_intIdEstado_cba INT(11) NOT NULL DEFAULT 1,
  ban_intIdBanco INT(11) NOT NULL,
  ccb_intIdCategoriaCuentaBancaria INT(11) NOT NULL,
  emp_intIdEmpresa INT(11) NOT NULL,
  usu_intIdCreador INT(11) NOT NULL,
  usu_intIdActualizador INT(11) NOT NULL,
  PRIMARY KEY (cba_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;
");
    }
}

