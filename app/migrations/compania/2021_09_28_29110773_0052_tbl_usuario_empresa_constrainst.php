<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblUsuarioEmpresaConstrainst52 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_usuario_empresa
 ADD CONSTRAINT FK_usuemp_IdActualizar FOREIGN KEY (usu_intIdActualizador)
REFERENCES tbl_usuario (usu_intId);");
    }
}
