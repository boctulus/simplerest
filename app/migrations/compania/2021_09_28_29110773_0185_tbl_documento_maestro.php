<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblDocumentoMaestro185 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_documento (
  doc_intId INT(11) NOT NULL AUTO_INCREMENT,
  doc_varDocumento VARCHAR(4) NOT NULL,
  doc_varDescripcion VARCHAR(150) NOT NULL,
  doc_bolEstado TINYINT(1) NOT NULL,
  doc_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
  doc_dtimFechaActualizacion DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  tra_intIdTransaccion INT(11) NOT NULL,
  usu_intIdCreador INT(11) NOT NULL,
  usu_intIdActualizador INT(11) NOT NULL,
  PRIMARY KEY (doc_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;");
    }
}

