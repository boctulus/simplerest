<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblRolConstraints4 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_rol
 ADD CONSTRAINT FK_rol_IdEstado FOREIGN KEY (est_intIdEstado_rol)
REFERENCES tbl_estado (est_intId);");
    }
}
