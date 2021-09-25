<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblUsuario implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_usuario
 ADD CONSTRAINT FK_usu_initIdCategoria FOREIGN KEY (cdo_intIdCategoriaDocumento)
REFERENCES tbl_categoria_documento (cdo_intId);");
    }
}

