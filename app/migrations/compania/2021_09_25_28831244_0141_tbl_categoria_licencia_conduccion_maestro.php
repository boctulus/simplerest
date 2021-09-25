<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblCategoriaLicenciaConduccion implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_categoria_licencia_conduccion (
  clc_intId int(11) NOT NULL AUTO_INCREMENT,
  clc_varNombre varchar(50) NOT NULL,
  clc_varDescripcion varchar(250) NOT NULL,
  clc_dtimFechaCreacion datetime NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  clc_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
  est_intIdEstado int(11) NOT NULL DEFAULT 1,
  usu_intIdCreador int(11) NOT NULL,
  usu_intIdActualizador int(11) NOT NULL,
  PRIMARY KEY (clc_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci,
COMMENT = ' * Descripcion: Tabla tbl_categoria_licencia_conduccion 
 * Author: http://www.divergente.net.co");
    }
}

