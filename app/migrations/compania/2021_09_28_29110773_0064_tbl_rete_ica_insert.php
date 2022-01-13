<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class TblRetencionIcaInsert371 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("INSERT INTO tbl_rete_ica 
        (
        ric_varReteIca, ric_intTope, ric_intPorcentaje, usu_intIdCreador, usu_intIdActualizador
        )
        VALUES 
        (
        'NO APLICA', 0, 1, 1,1
        );");
    }
}

