<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblCiudadConstrainst368 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_ciudad 
  ADD CONSTRAINT FK_ciudad_retencion_ica FOREIGN KEY (ica_intIdICA)
    REFERENCES tbl_retencion_ica(ric_intId);");
    }
}

