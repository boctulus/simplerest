<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblUsuarioEmpresaConstrainst49 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_usuario_empresa
 ADD CONSTRAINT FK_usuemp_Idempresa FOREIGN KEY (emp_intIdempresa)
REFERENCES tbl_empresa (emp_intId);
");
    }
}

