<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblGrupoProductoConstrainst227 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("ALTER TABLE tbl_grupo_producto 
  ADD CONSTRAINT FK_grp_IdCategoriaProducto FOREIGN KEY (cap_intIdCategoriaProducto)
    REFERENCES tbl_categoria_producto(cap_intId) ON DELETE NO ACTION ON UPDATE NO ACTION;");
    }
}

