<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblContacto implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_contacto 
  ADD CONSTRAINT FK_con_idCargo FOREIGN KEY (car_intIdcargo)
    REFERENCES tbl_cargo(car_intId);
");
    }
}

