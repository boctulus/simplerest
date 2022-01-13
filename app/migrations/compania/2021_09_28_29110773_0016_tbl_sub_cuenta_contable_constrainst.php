<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class TblSubCuentaContableConstrainst83 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_sub_cuenta_contable
        ADD INDEX FK_nat_intIdNatruralezaCuenta (nat_intIdNaturalezaCuentaContable);");

        Model::query("ALTER TABLE tbl_sub_cuenta_contable
        ADD INDEX FK_sub_IdCategoriaCuentaContable (ccc_intIdCategoriaCuentaContable);");

        Model::query("ALTER TABLE tbl_sub_cuenta_contable
        ADD INDEX FK_sub_idClase (cue_intIdCuentaContable);");

        Model::query("ALTER TABLE tbl_sub_cuenta_contable
        ADD CONSTRAINT FK_sub_IdMoneda FOREIGN KEY (mon_intIdMoneda)
        REFERENCES tbl_moneda (mon_intId);");
    }
    
}

