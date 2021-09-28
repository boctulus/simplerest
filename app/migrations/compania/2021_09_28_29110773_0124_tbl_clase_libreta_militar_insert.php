<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblClaseLibretaMilitarInsert124 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("INSERT INTO tbl_clase_libreta_militar (clm_varNombre,usu_intIdCreador, usu_intIdActualizador)
  VALUES ('PRIMERA CLASE', 1,1),
  ('SEGUNDA CLASE',  1,1);");
    }
}

