<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblRolPermisoInsert358 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("INSERT INTO tbl_rol_permiso(rpe_intId, rpe_dtimFechaCreacion, rpe_dtimFechaActualizacion, est_intIdEstado, rol_intIdRol, per_intIdPermiso, usu_intIdCreador, usu_intIdActualizador) VALUES
(1, '2021-09-06 03:26:55', '1000-01-01 00:00:00', 1, 1, 1, 1, 1);");
    }
}

