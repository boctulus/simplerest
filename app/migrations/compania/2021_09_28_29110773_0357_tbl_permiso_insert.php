<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblPermisoInsert357 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("INSERT INTO tbl_permiso(per_intId, per_varNombre, per_varDescripcion, per_dtimFechaCreacion, per_dtimFechaActualizacion, est_intIdEstado, usu_intIdCreador, usu_intIdActualizador) VALUES
(1, 'Agregar', 'Agregar', '2021-09-06 03:15:09', '0000-00-00 00:00:00', 1, 1, 1);");
    }
}

