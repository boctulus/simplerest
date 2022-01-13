<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class TblCategoriaPersonaPersonaMaestro178 implements IMigration
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

        Model::query("ALTER TABLE tbl_categoria_persona_persona 
        ADD INDEX FK_cpp_idCategoriaPersona(cap_intIdCategoriaPersona);");

        Model::query("ALTER TABLE tbl_categoria_persona_persona 
        ADD INDEX FK_cpp_idPersona(per_intIdPersona);");
    }
}

