<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblPreferenciasConstrainst385 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_preferencias 
  ADD CONSTRAINT FK_tpf_IdDocumento FOREIGN KEY (doc_intIdDocumento)
    REFERENCES tbl_documento(doc_intId);
");
    }
}

