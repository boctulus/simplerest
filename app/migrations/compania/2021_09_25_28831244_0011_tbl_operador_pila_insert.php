<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblOperadorPila implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("INSERT INTO tbl_operador_pila 
(
  opp_varCodigo, opp_varDescripcion, usu_intIdCreador, usu_intIdActualizador
)
VALUES 
(
  '01', 'SOI',1,1
),
(
  '02', 'MI PLANTILLA',1,1
),
(
  '03', 'APORTES EN LINEA',1,1
),
(
  '04', 'ASOPAGOS',1,1
),
(
  '05', 'FEDECAJAS (PILA FACIL)',1,1
),
(
  '06', 'SIMPLE',1,1
),
(
  '07', 'ARUS (ENLACE OPERATIVO)',1,1
);
");
    }
}

