<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class TblDepartamentoInsert360 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("INSERT INTO tbl_departamento( dep_varCodigoDepartamento, dep_varDepartamento, pai_intIdPais, usu_intIdCreador, usu_intIdActualizador) VALUES
        ('05', 'Antioquia',  1, 1, 1),
        ('08', 'Atlantico', 1, 1, 1),
        ('11', 'Bogota¡, D.C.', 1, 1, 1),
        ('13', 'Bolivar',  1, 1, 1),
        ('15', 'Boyaca¡', 1, 1, 1),
        ('17', 'Caldas', 1, 1, 1),
        ('18', 'Caqueta¡', 1, 1, 1),
        ('19', 'Cauca', 1, 1, 1),
        ('20', 'Cesar', 1, 1, 1),
        ('23', 'Cordoba',  1, 1, 1),
        ('25', 'Cundinamarca',  1, 1, 1),
        ('27', 'Choco',  1, 1, 1),
        ('41', 'Huila',  1, 1, 1),
        ('44', 'La Guajira',  1, 1, 1),
        ('47', 'Magdalena',  1, 1, 1),
        ('50', 'Meta',  1, 1, 1),
        ('52', 'Nariño',  1, 1, 1),
        ('54', 'Norte de Santander',  1, 1, 1),
        ('63', 'Quindio',  1, 1, 1),
        ('66', 'Risaralda',  1, 1, 1),
        ('68', 'Santander',  1, 1, 1),
        ('70', 'Sucre',  1, 1, 1),
        ('73', 'Tolima',  1, 1, 1),
        ('76', 'Valle del Cauca',  1, 1, 1),
        ('81', 'Arauca',  1, 1, 1),
        ('85', 'Casanare',  1, 1, 1),
        ('86', 'Putumayo',  1, 1, 1),
        ('88', 'Archipielago de San Andres, Providencia y Santa Catalina',  1, 1, 1),
        ('91', 'Amazonas',  1, 1, 1),
        ('94', 'Guainea',  1, 1, 1),
        ('95', 'Guaviare',  1, 1, 1),
        ('97', 'Vaupes',  1, 1, 1),
        ('99', 'Vichada',  1, 1, 1),
        ('0', 'Varios', 1, 1, 1);");
    }
}

