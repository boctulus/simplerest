<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblPersonaMaestro169 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_persona (
  per_intId INT(11) NOT NULL AUTO_INCREMENT,
  per_varIdentificacion VARCHAR(20) NOT NULL,
  per_varDV VARCHAR(1) NOT NULL,
  per_varRazonSocial VARCHAR(200) DEFAULT NULL,
  per_varNombre VARCHAR(100) DEFAULT NULL,
  per_varNombre2 VARCHAR(100) DEFAULT NULL,
  per_varApellido VARCHAR(100) DEFAULT NULL,
  per_varApellido2 VARCHAR(100) DEFAULT NULL,
  per_varNombreCompleto MEDIUMTEXT NOT NULL,
  per_varDireccion VARCHAR(255) NOT NULL,
  per_varCelular VARCHAR(15) NOT NULL,
  per_varTelefono VARCHAR(15) DEFAULT NULL,
  per_varEmail VARCHAR(100) NOT NULL,
  per_datFechaNacimiento DATE NOT NULL,
  per_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
  per_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
  tpr_intIdTipoPersona INT(11) NOT NULL DEFAULT 0,
  pai_intIdPais INT(11) NOT NULL,
  ciu_intIdCiudad INT(11) NOT NULL,
  gen_intIdGenero INT(11) NOT NULL DEFAULT 1,
  cid_intIdCategoriIdentificacion INT(11) NOT NULL,
  est_intIdEstado INT(11) NOT NULL DEFAULT 1,
  usu_intIdCreador INT(11) NOT NULL,
  usu_intIdActualizador INT(11) NOT NULL,
  PRIMARY KEY (per_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;");
    }
}

