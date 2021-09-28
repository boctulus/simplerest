<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblArlConstrainst31 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_arl
 ADD CONSTRAINT FK_arl_IdActualizador FOREIGN KEY (usu_intIdActualizador)
REFERENCES tbl_usuario (usu_intId);
");
    }
}

