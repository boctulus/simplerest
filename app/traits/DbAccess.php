<?php

namespace simplerest\traits;

use simplerest\libs\Factory;
use simplerest\libs\DB;
use simplerest\core\Model;
use simplerest\libs\Arrays;

trait DbAccess
{
    function getDbAccess($user_id) : Array {
        // casting o validación por seguridad
        $user_id = (int) $user_id;

        $dbs = Model::query("SELECT dba_varNombre FROM `tbl_usuarios_x_base_datos` as uxb
        INNER JOIN tbl_usuario_empresa as u ON u.use_intId = uxb.usu_intIdUsuario 
        INNER JOIN tbl_base_datos as db ON db.dba_intId = bas_intIdBasedatos
        WHERE uxb.`usu_intIdUsuario` = $user_id",  \PDO::FETCH_NUM);

        return array_column($dbs, 0);
    }
}
