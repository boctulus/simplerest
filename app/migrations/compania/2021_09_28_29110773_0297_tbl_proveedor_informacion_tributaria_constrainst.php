<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblProveedorInformacionTributariaConstrainst297 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_proveedor_informacion_tributaria 
        ADD CONSTRAINT FK_tip_IdActualizador7 FOREIGN KEY (usu_intIdActualizador)
            REFERENCES tbl_usuario(usu_intId);
        ");
    }
}

