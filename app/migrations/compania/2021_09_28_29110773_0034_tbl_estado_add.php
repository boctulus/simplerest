<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblEstadoADD34 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query(" ALTER TABLE tbl_estado ADD IF NOT EXISTS usu_intIdCreador INT NOT NULL DEFAULT 1;");
    }
}
