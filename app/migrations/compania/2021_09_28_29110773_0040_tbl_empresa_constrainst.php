<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblEmpresaConstrainst40 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_empresa
 ADD CONSTRAINT FK_emp_IdActualizador FOREIGN KEY (usu_intIdActualizador)
REFERENCES tbl_usuario (usu_intId);");
    }
}

