<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblRolPermisoConstrainst345 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_rol_permiso 
  ADD CONSTRAINT FK_rpe_IdRol FOREIGN KEY (rol_intIdRol)
    REFERENCES tbl_rol(rol_intId);
");
    }
}

