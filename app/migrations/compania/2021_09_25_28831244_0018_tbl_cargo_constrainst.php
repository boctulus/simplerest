<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblCargo implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_cargo
 ADD CONSTRAINT FK_car_IdEmpresa FOREIGN KEY (emp_intIdEmpresa)
REFERENCES tbl_empresa (emp_intId);");
    }
}

