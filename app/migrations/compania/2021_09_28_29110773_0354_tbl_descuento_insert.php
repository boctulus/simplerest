<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblDescuentoInsert354 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("INSERT INTO tbl_descuento(des_intId, des_varDescuento, des_decDescuento, des_dtimFechaCreacion, des_timFechaActualizacion, est_intIdEstado, usu_intIdCreador, usu_intIdActualizador) VALUES
(1, 'Descuento uno', 10.00, '2021-09-06 10:54:36', '0000-00-00 00:00:00', 1, 1, 1),
(2, 'Descuento dos', 20.00, '2021-09-06 10:54:36', '0000-00-00 00:00:00', 1, 1, 1),
(3, 'Descuento Tres', 30.00, '2021-09-06 10:54:36', '0000-00-00 00:00:00', 1, 1, 1);
");
    }
}
