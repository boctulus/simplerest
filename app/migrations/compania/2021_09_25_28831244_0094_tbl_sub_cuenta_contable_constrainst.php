<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblSubCuentaContable implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_sub_cuenta_contable
 ADD CONSTRAINT FK_sub_idCuentaContable FOREIGN KEY (cue_intIdCuentaContable)
REFERENCES tbl_cuenta_contable (cue_intId);");
    }
}

