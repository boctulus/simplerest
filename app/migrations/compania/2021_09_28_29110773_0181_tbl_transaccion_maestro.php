<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblTransaccionMaestro181 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_transaccion (
  tra_intId INT(11) NOT NULL AUTO_INCREMENT,
  tra_varTransaccion VARCHAR(25) NOT NULL,
  tra_bolEstado TINYINT(1) NOT NULL,
  tra_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
  tra_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
  usu_intIdCreador INT(11) NOT NULL,
  usu_intIdActualizador INT(11) NOT NULL,
  PRIMARY KEY (tra_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;
");
    }
}
