<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblMedioPago implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE `tbl_medio_pago`(
            `tmp_intId` int(11) NOT NULL  auto_increment , 
            `tmp_varCodigo` varchar(50) COLLATE utf8mb3_general_ci NOT NULL  , 
            `tmp_varNombre` varchar(100) COLLATE utf8mb3_general_ci NOT NULL  , 
            `tmp_lonDescripcion` longtext COLLATE utf8mb3_general_ci NOT NULL  , 
            `tmp_dtimFechaCreacion` datetime NOT NULL  DEFAULT current_timestamp(), 
            `tmp_dtimFechaActualizacion` datetime NOT NULL  DEFAULT '0000-00-00 00:00:00' , 
            `est_intIdEstado` int(11) NOT NULL  DEFAULT 1 , 
            `usu_intIdCreador` int(11) NOT NULL  , 
            `usu_intIdActualizador` int(11) NOT NULL  , 
            PRIMARY KEY (`tmp_intId`) , 
            KEY `FK_tmp_IdEstado`(`est_intIdEstado`) , 
            KEY `FK_tmp_IdCreado`(`usu_intIdCreador`) , 
            KEY `FK_tmp_IdActualizador`(`usu_intIdActualizador`) , 
            CONSTRAINT `FK_tmp_IdActualizador` 
            FOREIGN KEY (`usu_intIdActualizador`) REFERENCES `tbl_usuario` (`usu_intId`) , 
            CONSTRAINT `FK_tmp_IdCreador` 
            FOREIGN KEY (`usu_intIdCreador`) REFERENCES `tbl_usuario` (`usu_intId`) , 
            CONSTRAINT `FK_tmp_IdEstado` 
            FOREIGN KEY (`est_intIdEstado`) REFERENCES `tbl_estado` (`est_intId`) 
        ) ENGINE=InnoDB DEFAULT CHARSET='utf8mb3' COLLATE='utf8mb3_general_ci' COMMENT=' * Descripcion: Tabla tbl_medio_pago \r\n * Author: http://www.divergente.net.co\"/> Divergente Soluciones Informaticas S.A.S \r\n * DBA: Jose Perez.\r\n * Created: 2021-10-02 \r\n * Update:\r\n * Fecha Update:\r\n * Version Tabla: 1.0';
        ");
    }
}

