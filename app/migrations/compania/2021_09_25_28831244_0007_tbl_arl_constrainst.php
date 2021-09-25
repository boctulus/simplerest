<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblArl implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_arl
 ADD CONSTRAINT FK_ARL_IdEstado FOREIGN KEY (est_intIdEstado)
REFERENCES tbl_estado (est_intId);");
    }
}

