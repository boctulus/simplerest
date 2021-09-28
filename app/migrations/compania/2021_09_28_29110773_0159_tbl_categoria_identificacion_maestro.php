<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblCategoriaIdentificacionMaestro159 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_categoria_identificacion (
  cid_intId INT(11) NOT NULL AUTO_INCREMENT,
  cid_varCategoriaDocumento VARCHAR(100) NOT NULL,
  cid_varSiglas VARCHAR(3) NOT NULL,
  cid_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
  cid_dtimFechaActualizacion DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  est_intIdEstado INT(11) NOT NULL DEFAULT 1,
  usu_intIdCreador INT(11) NOT NULL,
  usu_intIdActualizador INT(11) NOT NULL,
  PRIMARY KEY (cid_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci,
COMMENT = ' * DescripciÃƒÂ³n: Tabla para registrar los diferentes tipos de Categoria Documento.
 * Author: http://www.divergente.net.co");
    }
}

