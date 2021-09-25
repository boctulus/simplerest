<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblConsecutivo implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_consecutivo 
  ADD CONSTRAINT FK_cse_idResolucion FOREIGN KEY (res_intIdResolucion)
    REFERENCES tbl_resolucion(res_intId);");
    }
}

