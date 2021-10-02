<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblPermisoMaestro203 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_permiso (
  per_intId INT(11) NOT NULL AUTO_INCREMENT,
  per_varNombre VARCHAR(50) NOT NULL,
  per_varDescripcion VARCHAR(100) NOT NULL,
  per_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
  per_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
  est_intIdEstado INT(11) NOT NULL DEFAULT 1,
  usu_intIdCreador INT(11) NOT NULL,
  usu_intIdActualizador INT(11) NOT NULL,
  PRIMARY KEY (per_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci,
COMMENT = ' * DescripciÃ³n: 
 * Author: http://www.divergente.net.co/> Divergente Soluciones Informaticas S.A.S 
 * DBA: Johan Esteban Carvajal Chalarca.
 * Created: 23/04/2020
 * Update:
 * Fecha Update:
 *Modulo: BackOffice
 * Version Tabla: 1.0';");
    }
}

