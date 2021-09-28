<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblCategoriaIdentificacionInsert163 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("INSERT INTO tbl_categoria_identificacion 
(
  cid_varCategoriaDocumento,cid_varSiglas,usu_intIdCreador,usu_intIdActualizador
)
VALUES 
(
  'Cedula de ciudadania','CC',1,1
),
(
  'Numero de identificacion tributaria','NIT',1,1
),
(
  'Cedula de extranjeria','CE',1,1
);");
    }
}

