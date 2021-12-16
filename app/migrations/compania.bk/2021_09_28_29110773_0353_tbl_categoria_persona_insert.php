<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblCategoriaPersonaInsert353 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("INSERT INTO tbl_categoria_persona(cap_intId, cap_varCategoriaPersona, cap_dtimFechaCreacion, cap_dtimFechaActualizacion, est_intIdEstado, usu_intIdCreador, usu_intIdActualizador) VALUES
(1, 'Empleado', '2021-05-20 11:40:29', '2021-06-30 09:38:58', 1, 1, 1),
(2, 'Tercero', '2021-05-20 11:40:58', '2021-07-21 21:44:46', 1, 1, 1),
(3, 'Visitante', '2021-06-25 15:21:04', '1000-01-01 00:00:00', 1, 1, 1),
(4, 'Cliente', '2021-08-04 16:48:49', '1000-01-01 00:00:00', 1, 1, 1),
(5, 'Proveedor', '2021-08-04 16:48:58', '1000-01-01 00:00:00', 1, 1, 1),
(6, 'Interesado', '2021-08-04 16:49:09', '1000-01-01 00:00:00', 1, 1, 1);");
    }
}

