<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblRolPermiso implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_rol_permiso 
  ADD CONSTRAINT FK_rpe_IdPermiso FOREIGN KEY (per_intIdPermiso)
    REFERENCES tbl_permiso(per_intId);
");
    }
}

