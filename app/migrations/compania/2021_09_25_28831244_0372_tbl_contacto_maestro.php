<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblContacto implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_contacto (
  con_intId INT(11) NOT NULL AUTO_INCREMENT,
  con_varNombreContacto VARCHAR(250) NOT NULL,
  con_varEmail VARCHAR(100) NOT NULL,
  con_varCelular VARCHAR(15) NOT NULL,
  con_varDireccion VARCHAR(250) DEFAULT NULL,
  con_varTelefono VARCHAR(10) DEFAULT NULL,
  con_varExtension VARCHAR(5) DEFAULT NULL,
  con_dtimFechaCreacion DATETIME NULL DEFAULT NULL,
  con_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
  est_intIdEstado INT(11) NOT NULL DEFAULT 0,
  emp_intIdEmpresa INT(11) NOT NULL,
  car_intIdcargo INT(11) NOT NULL,
  ciu_intIdCiudad INT(11) NOT NULL,
  pai_intIdPais INT(11) NOT NULL,
  usu_intIdCreador INT(11) NOT NULL,
  usu_intIdActualizador INT(11) NOT NULL,
  PRIMARY KEY (con_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;");
    }
}

