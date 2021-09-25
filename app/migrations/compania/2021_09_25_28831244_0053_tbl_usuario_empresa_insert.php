<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblUsuarioEmpresa implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("INSERT INTO tbl_usuario_empresa 
(
   usu_intIdUsuario, emp_intIdempresa, usu_intIdCreador, usu_intIdActualizador
)  
VALUES 
(
   1, 1, 1, 1
);");
    }
}

