<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblPais implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_pais
ADD CONSTRAINT FK_pai_idMoneda FOREIGN KEY (pai_intIdMoneda)
REFERENCES tbl_moneda (mon_intId);");
    }
}

