<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblClienteConstrainst246 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_cliente 
  ADD CONSTRAINT FK_cli_idPersona FOREIGN KEY (ali_intIdPersona)
    REFERENCES tbl_persona(per_intId);");
    }
}

