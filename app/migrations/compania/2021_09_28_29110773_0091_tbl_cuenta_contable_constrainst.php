<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblCuentaContableConstrainst91 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_cuenta_contable
 ADD UNIQUE INDEX cue_varNumeroCuenta (cue_varNumeroCuenta);");
    }
}
