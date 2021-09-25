<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblPersona implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_persona 
  ADD CONSTRAINT FK_per_idCategoriaIdentificacion FOREIGN KEY (cid_intIdCategoriIdentificacion)
    REFERENCES tbl_categoria_identificacion(cid_intId);");
    }
}

