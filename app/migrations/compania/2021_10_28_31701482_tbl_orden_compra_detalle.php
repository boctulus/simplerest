<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblOrdenCompraDetalle implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE `tbl_orden_compra_detalle`(
            `ocd_intId` int(11) NOT NULL  auto_increment , 
            `ocd_dateFecha` datetime NOT NULL  DEFAULT current_timestamp() , 
            `ocd_decValor` decimal(18,2) NOT NULL  , 
            `ocd_bolEstado` tinyint(4) NOT NULL  DEFAULT 1 , 
            `ocd_decCantidad` decimal(18,2) NOT NULL  , 
            `ocd_decCantidadOriginal` decimal(18,2) NOT NULL  , 
            `ocd_decCantidadPendiente` decimal(18,2) NOT NULL  , 
            `ocd_decCantidadRecibidad` decimal(18,2) NOT NULL  , 
            `ocd_decValorTotal` decimal(18,2) NOT NULL  , 
            `oco_varNumeroDocumento` varchar(20) COLLATE utf8mb3_general_ci NOT NULL  , 
            `ocd_varNota` varchar(250) COLLATE utf8mb3_general_ci NOT NULL  , 
            `ocd_dtimFechaCreacion` datetime NOT NULL  DEFAULT current_timestamp() , 
            `ocd_dtimFechaActualizacion` datetime NOT NULL  DEFAULT '1000-01-01 00:00:00' , 
            `ocd_dateFechaEntrega` date NOT NULL  , 
            `ocd_decIva` decimal(18,2) NOT NULL  , 
            `ocd_decPorceIVA` decimal(10,2) NOT NULL  , 
            `ocd_decPorcentajeDescuento` decimal(10,2) NOT NULL  , 
            `bod_intIdBodega` int(11) NOT NULL  DEFAULT 0 , 
            `pro_intIdProducto` int(11) NOT NULL  DEFAULT 0 , 
            `oco_intIdordenCompra` int(11) NOT NULL  , 
            `per_intIdPersona` int(11) NOT NULL  , 
            `doc_intIdDocumento` int(11) NOT NULL  , 
            `usu_intIdCreador` int(11) NOT NULL  , 
            `usu_intIdActualizador` int(11) NOT NULL  , 
            PRIMARY KEY (`ocd_intId`) , 
            KEY `FK_ocd_IdActualizador`(`usu_intIdActualizador`) , 
            KEY `FK_ocd_IdAliado`(`per_intIdPersona`) , 
            KEY `FK_ocd_IdCompras`(`oco_intIdordenCompra`) , 
            KEY `FK_ocd_IdCreador`(`usu_intIdCreador`) , 
            KEY `FK_ocd_IdDocumento`(`doc_intIdDocumento`) , 
            KEY `FK_ocd_IdProducto`(`pro_intIdProducto`) , 
            KEY `FK_ocd_IdBodega`(`bod_intIdBodega`) , 
            CONSTRAINT `FK_ocd_IdActualizador` 
            FOREIGN KEY (`usu_intIdActualizador`) REFERENCES `tbl_usuario` (`usu_intId`) , 
            CONSTRAINT `FK_ocd_IdAliado` 
            FOREIGN KEY (`per_intIdPersona`) REFERENCES `tbl_persona` (`per_intId`) , 
            CONSTRAINT `FK_ocd_IdBodega` 
            FOREIGN KEY (`bod_intIdBodega`) REFERENCES `tbl_bodega` (`bod_intId`) ON DELETE NO ACTION ON UPDATE NO ACTION , 
            CONSTRAINT `FK_ocd_IdCompras` 
            FOREIGN KEY (`oco_intIdordenCompra`) REFERENCES `tbl_orden_compra` (`oco_intId`) , 
            CONSTRAINT `FK_ocd_IdCreador` 
            FOREIGN KEY (`usu_intIdCreador`) REFERENCES `tbl_usuario` (`usu_intId`) , 
            CONSTRAINT `FK_ocd_IdDocumento` 
            FOREIGN KEY (`doc_intIdDocumento`) REFERENCES `tbl_documento` (`doc_intId`) , 
            CONSTRAINT `FK_ocd_IdProducto` 
            FOREIGN KEY (`pro_intIdProducto`) REFERENCES `tbl_producto` (`pro_intId`) 
        ) ENGINE=InnoDB DEFAULT CHARSET='utf8mb3' COLLATE='utf8mb3_general_ci';");
    }
}

