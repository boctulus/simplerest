<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblBarrio implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE `tbl_barrio`(
            `bar_intId` int(11) NOT NULL  auto_increment , 
            `bar_varCodigo` varchar(50) COLLATE utf8mb3_general_ci NOT NULL  , 
            `bar_varNombre` varchar(150) COLLATE utf8mb3_general_ci NOT NULL  , 
            `bar_lonDescripcion` longtext COLLATE utf8mb3_general_ci NOT NULL  , 
            `bar_dtimFechaCreacion` datetime NOT NULL  DEFAULT current_timestamp() , 
            `bar_dtimFechaActualizacion` datetime NOT NULL  DEFAULT '0000-00-00 00:00:00' , 
            `ciu_intIdciudad` int(11) NULL  , 
            `est_intIdEstado` int(11) NOT NULL  DEFAULT 1 , 
            `usu_intIdCreador` int(11) NOT NULL  , 
            `usu_intIdActualizador` int(11) NOT NULL  , 
            PRIMARY KEY (`bar_intId`) , 
            KEY `FK_bar_IdEstado`(`est_intIdEstado`) , 
            KEY `FK_bar_IdCreado`(`usu_intIdCreador`) , 
            KEY `FK_bar_IdActualizador`(`usu_intIdActualizador`) , 
            KEY `FK_bar_Idciudad`(`ciu_intIdciudad`) , 
            CONSTRAINT `FK_bar_IdActualizador` 
            FOREIGN KEY (`usu_intIdActualizador`) REFERENCES `tbl_usuario` (`usu_intId`) , 
            CONSTRAINT `FK_bar_IdCreador` 
            FOREIGN KEY (`usu_intIdCreador`) REFERENCES `tbl_usuario` (`usu_intId`) , 
            CONSTRAINT `FK_bar_IdEstado` 
            FOREIGN KEY (`est_intIdEstado`) REFERENCES `tbl_estado` (`est_intId`) , 
            CONSTRAINT `FK_bar_Idciudad` 
            FOREIGN KEY (`ciu_intIdciudad`) REFERENCES `tbl_ciudad` (`ciu_intId`) 
        ) ENGINE=InnoDB DEFAULT CHARSET='utf8mb3' COLLATE='utf8mb3_general_ci' COMMENT=' * Descripcion: Tabla tbl_barrio \r\n * Author: http://www.divergente.net.co\"/> Divergente Soluciones Informaticas S.A.S \r\n * DBA: Jose Perez.\r\n * Created: 2021-10-02 \r\n * Update:\r\n * Fecha Update:\r\n * Version Tabla: 1.0';
        ");
    }
}

