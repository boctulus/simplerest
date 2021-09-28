<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblPersonaConstrainst175 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_persona 
  ADD CONSTRAINT FK_per_idGenero FOREIGN KEY (gen_intIdGenero)
    REFERENCES tbl_genero(gen_intId);");
    }
}

