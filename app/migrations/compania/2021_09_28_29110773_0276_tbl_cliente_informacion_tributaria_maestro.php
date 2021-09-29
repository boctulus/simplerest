<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblClienteInformacionTributariaMaestro276 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_cliente_informacion_tributaria (
  tic_intId INT(11) NOT NULL AUTO_INCREMENT,
  tic_intGranContribuyente INT(50) NOT NULL,
  tic_intllevarContabilidad INT(250) NOT NULL,
  tic_intCalcularIca INT(11) NOT NULL,
  tic_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
  tic_dtimFechaActualizacion DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  sub_intIdSubcuentacontable INT(11) DEFAULT NULL,
  cli_intIdCliente INT(11) DEFAULT NULL,
  est_intIdEstado INT(11) NOT NULL DEFAULT 1,
  usu_intIdCreador INT(11) NOT NULL,
  usu_intIdActualizador INT(11) NOT NULL,
  PRIMARY KEY (tic_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci,
COMMENT = ' * Descripcion: Tabla tbl_cliente_informacion_tributaria 
 * Author: http://www.divergente.net.co';");
    }
}

