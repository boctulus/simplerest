<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblEmpresaConstrainst16 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_empresa
 ADD CONSTRAINT FK_emp_operador_pila FOREIGN KEY (opp_intIdOperador)
REFERENCES tbl_operador_pila (opp_intId);");
    }
}

