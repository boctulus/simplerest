<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblUsuarioConstrainst26 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_usuario
 ADD CONSTRAINT FK_usu_carIdCargo FOREIGN KEY (car_intIdCargo)
REFERENCES tbl_cargo (car_intId);
");
    }
}

