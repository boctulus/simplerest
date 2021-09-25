<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblPais implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("INSERT INTO tbl_pais(pai_varCodigo, pai_varPais, pai_varCodigoPaisCelular,pai_intIdMoneda, usu_intIdCreador, usu_intIdActualizador) VALUES
('169', 'Colombia', '+57',1,  1, 1),
('196', 'Costa Rica', '+50',1, 1, 1);");
    }
}

