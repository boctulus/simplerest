<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblMonedaInsert356 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("INSERT INTO tbl_moneda(mon_intId, mon_varCodigoMoneda, mon_varDescripcion, mon_dtimFechaCreacion, mon_dtimFechaActualizacion, est_intIdEstado, usu_intIdCreador, usu_intIdActualizador) VALUES
(1, '00001', 'Pesos', '2021-09-03 20:33:37', '0000-00-00 00:00:00', 1, 1, 1);");
    }
}

