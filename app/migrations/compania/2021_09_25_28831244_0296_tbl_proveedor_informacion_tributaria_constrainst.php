<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblProveedorInformacionTributaria implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_proveedor_informacion_tributaria 
  ADD CONSTRAINT FK_proveedor_informacion_tributaria_sub_cuenta_contable FOREIGN KEY (sub_intIdSubCuentaContable)
    REFERENCES tbl_sub_cuenta_contable(sub_intId);
");
    }
}

