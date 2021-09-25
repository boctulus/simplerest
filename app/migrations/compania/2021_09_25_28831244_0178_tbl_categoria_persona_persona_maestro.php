<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblCategoriaPersonaPersona implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_categoria_persona_persona (
  cpp_intId INT(11) NOT NULL AUTO_INCREMENT,
  per_intIdPersona INT(11) NOT NULL,
  cap_intIdCategoriaPersona INT(11) NOT NULL,
  cat_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (cpp_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;");
    }
}

