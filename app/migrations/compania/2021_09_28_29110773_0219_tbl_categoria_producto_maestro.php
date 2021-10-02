<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblCategoriaProductoMaestro219 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_categoria_producto (
  cap_intId INT(11) NOT NULL AUTO_INCREMENT,
  cap_varSiglaCategoriaProducto VARCHAR(50) NOT NULL,
  cap_varDescripcionCategoria VARCHAR(50) NOT NULL,
  cap_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
  cap_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
  usu_intIdCreador INT(11) NOT NULL,
  usu_intIdActualizador INT(11) NOT NULL,
  est_intIdEstado INT(11) DEFAULT 1,
  PRIMARY KEY (cap_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci,
COMMENT = '* Descripcion: 
 * Author: http://www.divergente.net.co';");
    }
}

