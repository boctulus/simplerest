<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblDocumentoConstrainst189 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_documento 
  ADD CONSTRAINT FK_doc_idTransaccion FOREIGN KEY (tra_intIdTransaccion)
    REFERENCES tbl_transaccion(tra_intId);");
    }
}

