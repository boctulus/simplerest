<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblClienteConstrainst244 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_cliente 
  ADD CONSTRAINT FK_cli_Descuento FOREIGN KEY (des_intIdDescuento)
    REFERENCES tbl_descuento(des_intId);");
    }
}

