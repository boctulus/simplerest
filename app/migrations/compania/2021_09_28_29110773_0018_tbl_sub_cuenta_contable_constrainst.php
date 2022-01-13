<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class TblSubCuentaContableConstrainst94 implements IMigration
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

