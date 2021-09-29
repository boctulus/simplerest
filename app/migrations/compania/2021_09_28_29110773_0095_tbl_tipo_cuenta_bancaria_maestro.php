<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblTipoCuentaBancariaMaestro95 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_tipo_cuenta_bancaria (
  tcb_intId int(11) NOT NULL AUTO_INCREMENT,
  tcb_varDescripcion varchar(50) NOT NULL,
  tcb_dtimFechaCreacion datetime NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  tcb_dtimFechaActualizacion datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  est_intIdEstado int(11) NOT NULL DEFAULT 1,
  usu_intIdCreador int(11) NOT NULL,
  usu_intIdActualizador int(11) NOT NULL,
  PRIMARY KEY (tcb_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci,
COMMENT = ' * Descripcion: Tabla tbl_tipo_cuenta_bancaria 
 * Author: http://www.divergente.net.co';");
    }
}

