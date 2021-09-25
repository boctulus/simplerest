<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblEstado implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE IF NOT EXISTS tbl_estado (
  est_intId INT(11) NOT NULL AUTO_INCREMENT,
  est_varNombre VARCHAR(20) NOT NULL,
  est_varIcono VARCHAR(100) NOT NULL,
  est_varColor VARCHAR(100) NOT NULL,
  est_dtimFechaCreacion DATETIME NULL DEFAULT NULL,
  est_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (est_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci,
COMMENT = '';");
    }
}

