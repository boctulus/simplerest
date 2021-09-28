<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblProveedorInformacionTributariaMaestro294 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_proveedor_informacion_tributaria (
  tip_intId INT(11) NOT NULL AUTO_INCREMENT,
  tip_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
  tip_dtimFechaActualizacion DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  sub_intIdSubCuentaContable INT(11) DEFAULT NULL,
  prv_intIdProveedor INT(11) DEFAULT NULL,
  est_intIdEstado INT(11) NOT NULL DEFAULT 1,
  usu_intIdCreador INT(11) NOT NULL,
  usu_intIdActualizador INT(11) NOT NULL,
  PRIMARY KEY (tip_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci,
COMMENT = ' * Descripcion: Tabla tbl_proveedor_informacion_tributaria 
 * Author: http://www.divergente.net.co");
    }
}

