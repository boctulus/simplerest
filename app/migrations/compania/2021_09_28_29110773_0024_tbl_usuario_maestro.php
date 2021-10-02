<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblUsuarioMaestro24 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_usuario (
  usu_intId int(11) NOT NULL AUTO_INCREMENT,
  usu_varNroIdentificacion varchar(50) NOT NULL,
  usu_varNombre varchar(50) NOT NULL,
  usu_varNombre2 varchar(50) NOT NULL DEFAULT '',
  usu_varApellido varchar(50) NOT NULL,
  usu_varApellido2 varchar(50) NOT NULL DEFAULT '',
  usu_varNombreCompleto varchar(100) NOT NULL,
  usu_varEmail varchar(50) NOT NULL,
  usu_varNumeroCelular varchar(20) NOT NULL DEFAULT '',
  usu_varExtension varchar(20) NOT NULL DEFAULT '',
  usu_varPassword varchar(64) NOT NULL,
  usu_varToken varchar(50) NOT NULL DEFAULT '',
  usu_varTokenContrasena varchar(100) NOT NULL DEFAULT '',
  usu_bolGetContrasena int(11) NOT NULL DEFAULT 0,
  usu_bolEstadoUsuario tinyint(1) NOT NULL DEFAULT 1,
  usu_varImagen varchar(250) NOT NULL DEFAULT '',
  usu_intNumeroIntentos int(11) NOT NULL DEFAULT 0,
  usu_dtimFechaCreacion datetime NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  usu_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
  usu_dtimFechaRecuperacion DATETIME NULL DEFAULT NULL,
  est_intIdEstado int(11) NOT NULL DEFAULT 1,
  rol_intIdRol int(11) NOT NULL,
  car_intIdCargo int(11) NOT NULL,
  cdo_intIdCategoriaDocumento int(11) NOT NULL,
  PRIMARY KEY (usu_intId)
)
ENGINE = INNODB,
AUTO_INCREMENT = 1,
AVG_ROW_LENGTH = 5461,
CHARACTER SET utf8,
COLLATE utf8_general_ci,
COMMENT = ' * Descripcion: 
 * Author: http://www.divergente.net.co';");
    }
}

