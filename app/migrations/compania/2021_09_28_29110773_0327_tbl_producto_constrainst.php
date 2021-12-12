<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblProductoConstrainst327 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Schema::FKcheck(false);
        
        $sc = new Schema('tbl_producto');
       
        $res = $sc
        ->foreign('unm_intIdUnidadMedida')
        ->references('unm_intId')
        ->on('tbl_unidadmedida')
        ->onDelete('cascade')
        ->onUpdate('restrict')
        ->change();

        Schema::FKcheck(true);

        //dd($sc->dd());
    }
}

