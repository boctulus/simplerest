<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblCotizacion implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE `tbl_cotizacion`(
            `cot_intId` int(11) NOT NULL  auto_increment , 
            `cot_varNumeroDocumento` varchar(20) COLLATE utf8mb3_general_ci NOT NULL  , 
            `cot_decCantidadTotal` decimal(18,2) NOT NULL  , 
            `cot_decBruto` decimal(18,2) NOT NULL  , 
            `cot_decDescuento` decimal(18,2) NOT NULL  , 
            `cot_decIVA` decimal(18,2) NOT NULL  , 
            `cot_dateFecha` date NOT NULL  , 
            `cot_decNeto` decimal(18,2) NOT NULL  , 
            `cot_bolEstado` tinyint(4) NOT NULL  DEFAULT 1 , 
            `cot_dateFechaVencimiento` date NOT NULL  , 
            `cot_dtimFechaCreacion` datetime NOT NULL  DEFAULT current_timestamp() , 
            `cot_dtimFechaActualizacion` datetime NOT NULL  DEFAULT '0000-00-00 00:00:00' , 
            `cot_varNota` longtext COLLATE utf8mb3_general_ci NOT NULL  , 
            `doc_intIdDocumento` int(11) NOT NULL  , 
            `cse_intIdConsecutivo` int(11) NOT NULL  , 
            `per_intIdPersona` int(11) NOT NULL  , 
            `usu_intIdCreador` int(11) NOT NULL  , 
            `usu_intIdActualizador` int(11) NOT NULL  , 
            PRIMARY KEY (`cot_intId`,`cot_varNumeroDocumento`) , 
            KEY `FK_cot_Actualizador`(`usu_intIdActualizador`) , 
            KEY `FK_cot_Creador`(`usu_intIdCreador`) , 
            KEY `FK_cot_IdConsecutivo`(`cse_intIdConsecutivo`) , 
            KEY `FK_cot_IdDocumento`(`doc_intIdDocumento`) , 
            KEY `FK_cot_idPersona`(`per_intIdPersona`) , 
            CONSTRAINT `FK_cot_IdConsecutivo` 
            FOREIGN KEY (`cse_intIdConsecutivo`) REFERENCES `tbl_consecutivo` (`cse_intId`) , 
            CONSTRAINT `FK_cot_IdDocumento` 
            FOREIGN KEY (`doc_intIdDocumento`) REFERENCES `tbl_documento` (`doc_intId`) , 
            CONSTRAINT `FK_cot_idActualizador` 
            FOREIGN KEY (`usu_intIdActualizador`) REFERENCES `tbl_usuario` (`usu_intId`) , 
            CONSTRAINT `FK_cot_idCreador` 
            FOREIGN KEY (`usu_intIdCreador`) REFERENCES `tbl_usuario` (`usu_intId`) , 
            CONSTRAINT `FK_cot_idPersona` 
            FOREIGN KEY (`per_intIdPersona`) REFERENCES `tbl_persona` (`per_intId`) 
        ) ENGINE=InnoDB DEFAULT CHARSET='utf8mb3' COLLATE='utf8mb3_general_ci';");
    }
}

