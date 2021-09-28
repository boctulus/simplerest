<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblEstadoInsert2 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("INSERT INTO tbl_estado 
(
   est_varNombre, est_varIcono, est_varColor
)
VALUES
(
  'Activo', 'NA', 'Rojo'
);");
    }
}

