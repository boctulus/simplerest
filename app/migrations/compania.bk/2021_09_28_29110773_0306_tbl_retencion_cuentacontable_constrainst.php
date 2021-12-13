<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblRetencionCuentacontableConstrainst306 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_retencion_cuentacontable 
  ADD CONSTRAINT FK_rec_intIdRetencion FOREIGN KEY (rec_intIdRetencion)
    REFERENCES tbl_retencion(ret_intId);
");
    }
}

