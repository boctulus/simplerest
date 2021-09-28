<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblDiasPagoInsert355 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("INSERT INTO tbl_dias_pago(dpa_intId, dpa_intDiasPago, dpa_dtimFechaCreacion, dpa_dtimFechaActualizacion, est_intIdEstado, usu_intIdCreador, usu_intIdActualizador) VALUES
(1, 30, '2021-09-06 10:53:05', '0000-00-00 00:00:00', 1, 1, 1),
(2, 60, '2021-09-06 10:53:05', '0000-00-00 00:00:00', 1, 1, 1),
(3, 90, '2021-09-06 10:53:05', '0000-00-00 00:00:00', 1, 1, 1);");
    }
}

