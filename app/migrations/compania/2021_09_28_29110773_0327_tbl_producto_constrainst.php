<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblProductoConstrainst327 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_producto 
  ADD CONSTRAINT FK_pro_IdUnidadMedida FOREIGN KEY (unm_intIdUnidadMedida)
    REFERENCES tbl_unidadmedida(unm_intId);

");
    }
}

