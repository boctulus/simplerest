<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblFactura implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_factura 
  ADD CONSTRAINT FK_fac_idConsecutivo FOREIGN KEY (cse_intIdConsecutivo)
    REFERENCES tbl_consecutivo(cse_intId);");
    }
}

