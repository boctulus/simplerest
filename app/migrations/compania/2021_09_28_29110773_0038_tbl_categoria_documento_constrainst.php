<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblCategoriaDocumentoConstrainst38 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_categoria_documento
 ADD CONSTRAINT FK_cat_IdActualizador FOREIGN KEY (usu_intIdActualizador)
REFERENCES tbl_usuario (usu_intId);");
    }
}
