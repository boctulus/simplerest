<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblOrdenCompra implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE `tbl_orden_compra`(
            `oco_intId` int(11) NOT NULL  auto_increment , 
            `oco_varNumeroDocumento` varchar(20) COLLATE utf8mb3_general_ci NOT NULL  , 
            `oco_decCantidadTotal` decimal(18,2) NOT NULL  , 
            `oco_decBruto` decimal(18,2) NOT NULL  , 
            `oco_decDescuento` decimal(18,2) NOT NULL  , 
            `oco_decIva` decimal(18,2) NOT NULL  DEFAULT 0.00 , 
            `oco_decIca` decimal(18,2) NOT NULL  DEFAULT 0.00 , 
            `oco_decRetencionfuente` decimal(18,2) NOT NULL  DEFAULT 0.00 , 
            `oco_decReteIva` decimal(18,2) NOT NULL  DEFAULT 0.00 , 
            `oco_dateFecha` date NOT NULL  DEFAULT current_timestamp() , 
            `oco_decNeto` decimal(18,2) NOT NULL  , 
            `oco_decPorceRetefuente` decimal(10,2) NOT NULL  DEFAULT 0.00 , 
            `oco_intTopeRetefuente` int(11) NOT NULL  DEFAULT 0 , 
            `oco_decPorceReteiva` decimal(10,2) NOT NULL  DEFAULT 0.00 , 
            `oco_intTopeReteiva` int(11) NOT NULL  DEFAULT 0 , 
            `oco_decPorceIca` decimal(10,2) NOT NULL  , 
            `oco_intTopeReteIca` int(11) NOT NULL  DEFAULT 0 , 
            `oco_bolEstado` tinyint(4) NOT NULL  DEFAULT 1 , 
            `oco_bolNotificacion` tinyint(4) NULL  DEFAULT 1 , 
            `oco_dtimFechaCreacion` datetime NOT NULL  DEFAULT current_timestamp() , 
            `oco_dtimFechaActualizacion` datetime NOT NULL  DEFAULT '0000-00-00 00:00:00' , 
            `oco_varNota` varchar(250) COLLATE utf8mb3_general_ci NULL  , 
            `cen_intIdCentrocostos` int(11) NULL  DEFAULT 1 , 
            `doc_intDocumento` int(11) NOT NULL  DEFAULT 1 , 
            `cse_intIdConsecutivo` int(11) NOT NULL  DEFAULT 1 , 
            `per_intIdPersona` int(11) NOT NULL  DEFAULT 1 , 
            `usu_intIdCreador` int(11) NOT NULL  DEFAULT 1 , 
            `usu_intIdActualizador` int(11) NOT NULL  DEFAULT 1 , 
            PRIMARY KEY (`oco_intId`) , 
            KEY `FK_oco_IdCentroCostos`(`cen_intIdCentrocostos`) , 
            KEY `FK_oco_IdConsecutivo`(`cse_intIdConsecutivo`) , 
            KEY `FK_oco_IdDocumento`(`doc_intDocumento`) , 
            KEY `FK_oco_IdUsuarioActualizador`(`usu_intIdActualizador`) , 
            KEY `FK_oco_IdUsuarioCreador`(`usu_intIdCreador`) , 
            KEY `FK_oco_idPersona`(`per_intIdPersona`) , 
            CONSTRAINT `FK_oco_IdCentroCosto` 
            FOREIGN KEY (`cen_intIdCentrocostos`) REFERENCES `tbl_centro_costos` (`cco_intId`) , 
            CONSTRAINT `FK_oco_IdConsecutivo` 
            FOREIGN KEY (`cse_intIdConsecutivo`) REFERENCES `tbl_consecutivo` (`cse_intId`) , 
            CONSTRAINT `FK_oco_IdDocumento` 
            FOREIGN KEY (`doc_intDocumento`) REFERENCES `tbl_documento` (`doc_intId`) , 
            CONSTRAINT `FK_oco_IdUsuarioActualizador` 
            FOREIGN KEY (`usu_intIdActualizador`) REFERENCES `tbl_usuario` (`usu_intId`) , 
            CONSTRAINT `FK_oco_IdUsuarioCreador` 
            FOREIGN KEY (`usu_intIdCreador`) REFERENCES `tbl_usuario` (`usu_intId`) , 
            CONSTRAINT `FK_oco_idPersona` 
            FOREIGN KEY (`per_intIdPersona`) REFERENCES `tbl_persona` (`per_intId`) 
        ) ENGINE=InnoDB DEFAULT CHARSET='utf8mb3' COLLATE='utf8mb3_general_ci';
        ");
    }
}

