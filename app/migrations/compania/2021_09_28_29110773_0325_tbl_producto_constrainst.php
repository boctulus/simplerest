<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblProductoConstrainst325 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_producto 
  ADD CONSTRAINT FK_pro_IdIva FOREIGN KEY (iva_intIdIva)
    REFERENCES tbl_iva(iva_intId);

");
    }
}

