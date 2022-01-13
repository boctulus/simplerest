<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class TblUbicacion implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE `tbl_ubicacion`(
            `ubi_intId` int(11) NOT NULL  auto_increment , 
            `ubi_varCodigo` varchar(50) COLLATE utf8mb3_general_ci NOT NULL  , 
            `ubi_varNombre` varchar(100) COLLATE utf8mb3_general_ci NOT NULL  , 
            `ubi_lonDescripcion` longtext COLLATE utf8mb3_general_ci NOT NULL  , 
            `ubi_dtimFechaCreacion` datetime NOT NULL  DEFAULT current_timestamp() , 
            `ubi_dtimFechaActualizacion` datetime NOT NULL  DEFAULT '1000-01-01 00:00:00' , 
            `est_intIdEstado` int(11) NOT NULL  DEFAULT 1 , 
            `usu_intIdCreador` int(11) NOT NULL  , 
            `usu_intIdActualizador` int(11) NOT NULL  , 
            PRIMARY KEY (`ubi_intId`) , 
            KEY `FK_ubi_IdEstado`(`est_intIdEstado`) , 
            KEY `FK_ubi_IdCreado`(`usu_intIdCreador`) , 
            KEY `FK_ubi_IdActualizador`(`usu_intIdActualizador`) , 
            CONSTRAINT `FK_ubi_IdActualizador` 
            FOREIGN KEY (`usu_intIdActualizador`) REFERENCES `tbl_usuario` (`usu_intId`) , 
            CONSTRAINT `FK_ubi_IdCreador` 
            FOREIGN KEY (`usu_intIdCreador`) REFERENCES `tbl_usuario` (`usu_intId`) , 
            CONSTRAINT `FK_ubi_IdEstado` 
            FOREIGN KEY (`est_intIdEstado`) REFERENCES `tbl_estado` (`est_intId`) 
        ) ENGINE=InnoDB DEFAULT CHARSET='utf8mb3' COLLATE='utf8mb3_general_ci' COMMENT=' * Descripcion: Tabla tbl_ubicacion \r\n * Author: http://www.divergente.net.co\"/> Divergente Soluciones Informaticas S.A.S \r\n * DBA: Jose Perez.\r\n * Created: 2021-10-02 \r\n * Update:\r\n * Fecha Update:\r\n * Version Tabla: 1.0';
        ");
    }
}

