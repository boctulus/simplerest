<?php

namespace simplerest\traits;

use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;
use simplerest\core\Model;
use simplerest\core\libs\Arrays;

/*
    Example
*/

trait DbAccess
{
    /*
        Retorna un array con los nombres de las bases de datos a las que un usuario tiene acceso
    */
    function getDbAccess($user_id) : Array {
        // casting o validación por seguridad
        $user_id = (int) $user_id;

        /*
            Todas las bases de datos (de empresas) de un usuario
        */
        $dbs = DB::select("SELECT dba_varNombre FROM `tbl_usuarios_x_base_datos` as uxb
        INNER JOIN tbl_usuario_empresa as u ON u.use_intId = uxb.usu_intIdUsuario 
        INNER JOIN tbl_base_datos as db ON db.dba_intId = bas_intIdBasedatos
        WHERE uxb.`usu_intIdUsuario` = ?", [$user_id], 'NUM');

        return array_column($dbs, 0);
    }
}
