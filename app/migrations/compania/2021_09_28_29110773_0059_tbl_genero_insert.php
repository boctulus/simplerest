<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblGeneroInsert59 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("INSERT INTO tbl_genero
(
   gen_varGenero, usu_intIdCreador, usu_intIdActualizador
)
VALUES 
('Masculino', 1, 1)
,('Femenino',1,1);");
    }
}

