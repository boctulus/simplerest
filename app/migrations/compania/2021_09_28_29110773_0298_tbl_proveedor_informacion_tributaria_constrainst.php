<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblProveedorInformacionTributariaConstrainst298 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_proveedor_informacion_tributaria 
        ADD CONSTRAINT FK_tip_IdCreador7 FOREIGN KEY (usu_intIdCreador)
            REFERENCES tbl_usuario(usu_intId);
        ");
    }
}

