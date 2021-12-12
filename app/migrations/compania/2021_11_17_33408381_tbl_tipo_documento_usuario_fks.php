<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblTipoDocumentoUsuarioFks implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        $sc = new Schema('tbl_tipo_documento');

        $sc->foreign('usu_intIdCreador')
        ->references('usu_intId')
        ->on('tbl_usuario');
        
        $sc->foreign('usu_intIdActualizador')
        ->references('usu_intId')
        ->on('tbl_usuario');

        // ALTER TABLE
        $sc->change(); 
    }
}

