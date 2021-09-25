<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblRetencionCuentacontable implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_retencion_cuentacontable (
  rec_intId INT(11) NOT NULL AUTO_INCREMENT,
  rec_intIdRetencion INT(11) NOT NULL,
  rec_intIdCuentaContable INT(11) NOT NULL,
  rec_dtimFechaCreacion DATETIME NULL DEFAULT NULL,
  rec_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
  usu_intIdCreador INT(11) NOT NULL,
  usu_intIdActualizador INT(11) NOT NULL,
  PRIMARY KEY (rec_intId, rec_intIdRetencion, rec_intIdCuentaContable)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;
");
    }
}

