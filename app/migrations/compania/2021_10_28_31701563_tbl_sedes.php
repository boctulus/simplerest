<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class TblSedes implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE `tbl_sede`(
            `sed_intId` int(11) NOT NULL  auto_increment , 
            `sed_varCodigo` varchar(100) COLLATE utf8mb3_general_ci  NULL  , 
            `sed_varNombre` varchar(100) COLLATE utf8mb3_general_ci NOT NULL  , 
            `sed_lonDescripcion` Longtext COLLATE utf8mb3_general_ci  NULL  , 
            `sed_dtimFechaCreacion` datetime NOT NULL  DEFAULT current_timestamp() , 
            `sed_dtimFechaActualizacion` datetime NOT NULL  DEFAULT '1000-01-01 00:00:00' , 
            `emn_intIdEmpresa` int(11) NULL  , 
            `est_intIdEstado` int(11) NOT NULL  DEFAULT 1 , 
            `usu_intIdCreador` int(11) NOT NULL  , 
            `usu_intIdActualizador` int(11) NOT NULL  , 
            PRIMARY KEY (`sed_intId`) , 
            KEY `FK_sed_IdEstado`(`est_intIdEstado`) , 
            KEY `FK_sed_IdCreado`(`usu_intIdCreador`) , 
            KEY `FK_sed_IdActualizador`(`usu_intIdActualizador`) , 
            KEY `FK_sed_IdEmpresa`(`emn_intIdEmpresa`) , 
            CONSTRAINT `FK_sed_IdActualizador` 
            FOREIGN KEY (`usu_intIdActualizador`) REFERENCES `tbl_usuario` (`usu_intId`) , 
            CONSTRAINT `FK_sed_IdCreador` 
            FOREIGN KEY (`usu_intIdCreador`) REFERENCES `tbl_usuario` (`usu_intId`) , 
            CONSTRAINT `FK_sed_IdEmpresa` 
            FOREIGN KEY (`emn_intIdEmpresa`) REFERENCES `tbl_empresa_nomina` (`emn_intId`) , 
            CONSTRAINT `FK_sed_IdEstado` 
            FOREIGN KEY (`est_intIdEstado`) REFERENCES `tbl_estado` (`est_intId`) 
        ) ENGINE=InnoDB DEFAULT CHARSET='utf8mb3' COLLATE='utf8mb3_general_ci' COMMENT=' * Descripcion: Tabla tbl_sedes \r\n * Author: http://www.divergente.net.co\"/> Divergente Soluciones Informaticas S.A.S \r\n * DBA: Jose Perez.\r\n * Created: 2021-08-21 \r\n * Update:\r\n * Fecha Update:\r\n * Version Tabla: 1.0';
        ");
    }
}

