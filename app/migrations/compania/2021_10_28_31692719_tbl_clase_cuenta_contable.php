<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblClaseCuentaContable implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE `tbl_clase_cuenta_contable`(
            `cla_intId` int(11) NOT NULL  auto_increment , 
            `cla_intCodigo` int(11) NOT NULL  , 
            `cla_varNombre` varchar(50) COLLATE utf8mb3_general_ci NOT NULL  , 
            `cla_dtimFechaCreacion` datetime NOT NULL  DEFAULT current_timestamp() , 
            `cla_dtimFechaActualizacion` datetime NOT NULL  DEFAULT '0000-00-00 00:00:00' , 
            `nat_intId` int(11) NOT NULL  DEFAULT 0 , 
            `usu_intIdCreador` int(11) NOT NULL  DEFAULT 1, 
            `usu_intIdActualizador` int(11) NOT NULL  DEFAULT 1, 
            PRIMARY KEY (`cla_intId`,`cla_intCodigo`) , 
            KEY `FK_cla_idActualizador`(`usu_intIdActualizador`) , 
            KEY `FK_cla_idCreador`(`usu_intIdCreador`) , 
            KEY `FK_cla_idNaturaleza`(`nat_intId`) , 
            CONSTRAINT `FK_cla_idActualizador` 
            FOREIGN KEY (`usu_intIdActualizador`) REFERENCES `tbl_usuario` (`usu_intId`) , 
            CONSTRAINT `FK_cla_idCreador` 
            FOREIGN KEY (`usu_intIdCreador`) REFERENCES `tbl_usuario` (`usu_intId`) , 
            CONSTRAINT `FK_cla_idNaturaleza` 
            FOREIGN KEY (`nat_intId`) REFERENCES `tbl_naturaleza_cuenta_contable` (`ncc_intId`) 
        ) ENGINE=InnoDB DEFAULT CHARSET='utf8mb3' COLLATE='utf8mb3_general_ci' COMMENT=' * Descripción: \r\n * Author: http://www.divergente.net.co\"/> Divergente Soluciones Informaticas S.A.S \r\n * DBA: Carlos Daniel Grajales Gil\r\n * Created: 10/07/2020\r\n * Update:\r\n * Fecha Update:\r\n *Modulo: BackOffice\r\n * Version Tabla: 1.0';
        ");

        Model::query("INSERT INTO `tbl_clase_cuenta_contable` (`cla_intId`, `cla_intCodigo`, `cla_varNombre`, `cla_dtimFechaCreacion`, `cla_dtimFechaActualizacion`, `nat_intId`, `usu_intIdCreador`, `usu_intIdActualizador`) VALUES
        (1, 1, 'Activo', '2020-07-16 17:22:30', '0000-00-00 00:00:00', 2, 1, 1),
        (2, 2, 'Pasivo', '2020-07-16 17:22:48', '0000-00-00 00:00:00', 2, 1, 1),
        (3, 3, 'Patrimonio', '2020-07-16 17:24:17', '0000-00-00 00:00:00', 1, 1, 1),
        (4, 4, 'Ingresos', '2020-07-16 17:24:42', '0000-00-00 00:00:00', 1, 1, 1),
        (5, 5, 'Gastos', '2020-07-16 17:25:17', '0000-00-00 00:00:00', 2, 1, 1),
        (6, 6, 'Costos de Ventas', '2021-02-02 11:51:29', '0000-00-00 00:00:00', 2, 1, 1),
        (7, 7, 'Costos de Produción o de Operacion', '2021-02-02 11:51:52', '0000-00-00 00:00:00', 2, 1, 1),
        (8, 8, 'Cuentas de Orden Deudoras', '2021-10-19 16:52:16', '0000-00-00 00:00:00', 2, 1, 1),
        (9, 9, 'Cuentas de orden Acreedoras', '2021-10-19 16:53:06', '0000-00-00 00:00:00', 1, 1, 1);");
    }
}

