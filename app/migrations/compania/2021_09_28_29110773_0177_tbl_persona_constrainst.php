<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblPersonaConstrainst177 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_persona 
  ADD CONSTRAINT FK_per_idTipoPersona FOREIGN KEY (tpr_intIdTipoPersona)
    REFERENCES tbl_tipo_persona(tpr_intId) ON DELETE NO ACTION ON UPDATE NO ACTION;");
    }
}

