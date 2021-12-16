<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblCotizacionDetalle implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE `tbl_cotizacion_detalle`(
            `cde_intId` int(11) NOT NULL  auto_increment , 
            `cde_fecha` date NOT NULL  , 
            `cde_decValor` decimal(18,2) NOT NULL  , 
            `cde_bolEstado` tinyint(4) NOT NULL  DEFAULT 1 , 
            `cde_decCantidad` decimal(18,2) NOT NULL  , 
            `cde_decValorTotal` decimal(18,2) NOT NULL  , 
            `cde_decPorcentajeIva` decimal(18,2) NOT NULL  , 
            `cde_decValorIva` decimal(18,2) NOT NULL  , 
            `cde_decPorcentajeDescuento` decimal(18,2) NOT NULL  , 
            `cde_decValorDescuento` decimal(18,2) NOT NULL  , 
            `cde_dtimFechaCreacion` datetime NOT NULL  DEFAULT current_timestamp() , 
            `cde_dtimFechaActualizacion` datetime NOT NULL  DEFAULT '1000-01-01 00:00:00' , 
            `cot_intNroDocumento` varchar(20) COLLATE utf8mb3_general_ci NULL  , 
            `cde_varDescripcion` longtext COLLATE utf8mb3_general_ci NOT NULL  , 
            `cot_intIdCotizacion` int(11) NOT NULL  , 
            `pro_intIdProducto` int(11) NOT NULL  DEFAULT 0 , 
            `doc_intIdDocumento` int(11) NOT NULL  , 
            `usu_intIdCreador` int(11) NOT NULL  , 
            `usu_intIdActualizador` int(11) NOT NULL  , 
            PRIMARY KEY (`cde_intId`) , 
            KEY `FK_cde1_idActualizador`(`usu_intIdActualizador`) , 
            KEY `FK_cde1_idCreador`(`usu_intIdCreador`) , 
            KEY `FK_cde_IdCotizacion`(`cot_intIdCotizacion`) , 
            KEY `FK_cde_IdDocumento`(`doc_intIdDocumento`) , 
            KEY `FK_cde_IdProducto`(`pro_intIdProducto`) , 
            CONSTRAINT `FK_cde1_idActualizador` 
            FOREIGN KEY (`usu_intIdActualizador`) REFERENCES `tbl_usuario` (`usu_intId`) , 
            CONSTRAINT `FK_cde1_idCreador` 
            FOREIGN KEY (`usu_intIdCreador`) REFERENCES `tbl_usuario` (`usu_intId`) , 
            CONSTRAINT `FK_cde_IdCotizacion` 
            FOREIGN KEY (`cot_intIdCotizacion`) REFERENCES `tbl_cotizacion` (`cot_intId`) , 
            CONSTRAINT `FK_cde_IdDocumento` 
            FOREIGN KEY (`doc_intIdDocumento`) REFERENCES `tbl_documento` (`doc_intId`) , 
            CONSTRAINT `FK_cde_IdProducto` 
            FOREIGN KEY (`pro_intIdProducto`) REFERENCES `tbl_producto` (`pro_intId`) 
        ) ENGINE=InnoDB DEFAULT CHARSET='utf8mb3' COLLATE='utf8mb3_general_ci';");
    }
}

