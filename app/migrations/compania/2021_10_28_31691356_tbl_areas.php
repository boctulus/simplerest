<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblAreas implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE `tbl_area`(
            `are_intId` int(11) NOT NULL  auto_increment , 
            `are_varCodigo` varchar(100) COLLATE utf8mb3_general_ci  NULL  , 
            `are_varNombre` varchar(100) COLLATE utf8mb3_general_ci NOT NULL  , 
            `are_lonDescripcion` LONGTEXT COLLATE utf8mb3_general_ci  NULL  , 
            `are_dtimFechaCreacion` datetime NOT NULL  DEFAULT current_timestamp() , 
            `are_dtimFechaActualizacion` datetime NOT NULL  DEFAULT '0000-00-00 00:00:00' , 
            `emn_intIdEmpresaNomina` int(11) NULL  , 
            `est_intIdEstado` int(11) NOT NULL  DEFAULT 1 , 
            `usu_intIdCreador` int(11) NOT NULL  , 
            `usu_intIdActualizador` int(11) NOT NULL  , 
            PRIMARY KEY (`are_intId`) , 
            KEY `FK_are_IdEstado`(`est_intIdEstado`) , 
            KEY `FK_are_IdCreado`(`usu_intIdCreador`) , 
            KEY `FK_are_IdActualizador`(`usu_intIdActualizador`) , 
            KEY `FK_are_IdEmpresaNomina`(`emn_intIdEmpresaNomina`) , 
            CONSTRAINT `FK_are_IdActualizador` 
            FOREIGN KEY (`usu_intIdActualizador`) REFERENCES `tbl_usuario` (`usu_intId`) , 
            CONSTRAINT `FK_are_IdCreador` 
            FOREIGN KEY (`usu_intIdCreador`) REFERENCES `tbl_usuario` (`usu_intId`) , 
            CONSTRAINT `FK_are_IdEmpresaNomina` 
            FOREIGN KEY (`emn_intIdEmpresaNomina`) REFERENCES `tbl_empresa_nomina` (`emn_intId`) , 
            CONSTRAINT `FK_are_IdEstado` 
            FOREIGN KEY (`est_intIdEstado`) REFERENCES `tbl_estado` (`est_intId`) 
        ) ENGINE=InnoDB DEFAULT CHARSET='utf8mb3' COLLATE='utf8mb3_general_ci' COMMENT=' * Descripcion: Tabla tbl_areas \r\n * Author: http://www.divergente.net.co\"/> Divergente Soluciones Informaticas S.A.S \r\n * DBA: Jose Perez.\r\n * Created: 2021-08-21 \r\n * Update:\r\n * Fecha Update:\r\n * Version Tabla: 1.0';
        ");
    }
}

