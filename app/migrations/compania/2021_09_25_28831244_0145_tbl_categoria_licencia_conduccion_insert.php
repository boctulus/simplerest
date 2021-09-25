<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblCategoriaLicenciaConduccion implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("INSERT INTO tbl_categoria_licencia_conduccion (clc_varNombre,clc_varDescripcion,usu_intIdCreador, usu_intIdActualizador)
  VALUES ('A2','Para motocicletas, motociclos y mototriciclos de mÃƒÂ¡s de 125 CC de cilindrada.',1,1),
  ('B1','Para autÃƒÂ³moviles, motocarros, cuatrimotos, camperos, camionetas y microbuses de servicio particular.',1,1),
  ('C1','Para automÃƒÂ³viles, camperos, camionetas y microbuses de servicio pÃƒÂºblico.',1,1),
  ('C2','Para conducir camiones rÃƒÂ­gidos, buses ybusetas de servicio particular. Antigua CategorÃƒÂ­a 5.',1,1);");
    }
}

