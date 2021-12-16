<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblEmpresaNomina implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE `tbl_empresa_nomina`(
            `emn_intId` int(11) NOT NULL  auto_increment , 
            `emn_varRazonSocial` varchar(300) COLLATE utf8mb3_general_ci NOT NULL  , 
            `emn_varNit` varchar(20) COLLATE utf8mb3_general_ci NOT NULL  , 
            `emn_varEmail` varchar(100) COLLATE utf8mb3_general_ci NOT NULL  , 
            `emn_varCelular` varchar(50) COLLATE utf8mb3_general_ci NOT NULL  , 
            `emn_varTipoCuenta` varchar(20) COLLATE utf8mb3_general_ci NOT NULL  , 
            `emn_varNumeroCuenta` varchar(50) COLLATE utf8mb3_general_ci NOT NULL  , 
            `emn_varPila` varchar(350) COLLATE utf8mb3_general_ci NOT NULL  , 
            `emn_intAnoConstitucion` int(11) NOT NULL  DEFAULT 0 , 
            `emn_tinAplicaLey14292021` tinyint(4) NOT NULL  DEFAULT 0 , 
            `emn_tinAplicaLey5902000` tinyint(4) NOT NULL  DEFAULT 0 , 
            `emn_tinAporteParas16072012` tinyint(4) NOT NULL  DEFAULT 0 , 
            `emn_tinDecreto5582020` tinyint(4) NOT NULL  DEFAULT 0 , 
            `emn_dtimFechaCreacion` datetime NOT NULL  DEFAULT current_timestamp() , 
            `emn_dtimFechaActualizacion` datetime NOT NULL  DEFAULT '1000-01-01 00:00:00' , 
            `arl_intIdArl` int(11) NULL  , 
            `per_intIdOperador` int(11) NULL  , 
            `est_intIdEstado` int(11) NOT NULL  DEFAULT 1 , 
            `usu_intIdCreador` int(11) NOT NULL  , 
            `usu_intIdActualizador` int(11) NOT NULL  , 
            PRIMARY KEY (`emn_intId`) , 
            KEY `FK_emn_IdEstado`(`est_intIdEstado`) , 
            KEY `FK_emn_IdCreado`(`usu_intIdCreador`) , 
            KEY `FK_emn_IdActualizador`(`usu_intIdActualizador`) , 
            KEY `FK_emn_IdOperador`(`per_intIdOperador`) , 
            KEY `FK_emn_IdArl`(`arl_intIdArl`) , 
            CONSTRAINT `FK_emn_IdActualizador` 
            FOREIGN KEY (`usu_intIdActualizador`) REFERENCES `tbl_usuario` (`usu_intId`) , 
            CONSTRAINT `FK_emn_IdArl` 
            FOREIGN KEY (`arl_intIdArl`) REFERENCES `tbl_arl` (`arl_intId`) , 
            CONSTRAINT `FK_emn_IdCreador` 
            FOREIGN KEY (`usu_intIdCreador`) REFERENCES `tbl_usuario` (`usu_intId`) , 
            CONSTRAINT `FK_emn_IdEstado` 
            FOREIGN KEY (`est_intIdEstado`) REFERENCES `tbl_estado` (`est_intId`) , 
            CONSTRAINT `FK_emn_IdOperador` 
            FOREIGN KEY (`per_intIdOperador`) REFERENCES `tbl_persona` (`per_intId`) 
        ) ENGINE=InnoDB DEFAULT CHARSET='utf8mb3' COLLATE='utf8mb3_general_ci' COMMENT=' * Descripcion: Tabla tbl_emnresa \r\n * Author: http://www.divergente.net.co\"/> Divergente Soluciones Informaticas S.A.S \r\n * DBA: Jose Perez.\r\n * Created: 2021-08-21 \r\n * Update:\r\n * Fecha Update:\r\n * Version Tabla: 1.0';
        
        ");
    }
}

