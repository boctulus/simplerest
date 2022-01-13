<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class TblFrecuencia implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE `tbl_frecuencia`(
            `fre_intId` int(11) NOT NULL  auto_increment , 
            `fre_varCodigo` varchar(50) COLLATE utf8mb3_general_ci NOT NULL  , 
            `fre_varNombre` varchar(150) COLLATE utf8mb3_general_ci NOT NULL  , 
            `fre_lonDescripcion` longtext COLLATE utf8mb3_general_ci NOT NULL  , 
            `fre_intPeriodicidad` int(11) NOT NULL  , 
            `fre_dtimFechaCreacion` datetime NOT NULL  DEFAULT current_timestamp() , 
            `fre_dtimFechaActualizacion` datetime NOT NULL  DEFAULT '1000-01-01 00:00:00' , 
            `est_intIdEstado` int(11) NOT NULL  DEFAULT 1 , 
            `usu_intIdCreador` int(11) NOT NULL  , 
            `usu_intIdActualizador` int(11) NOT NULL  , 
            PRIMARY KEY (`fre_intId`) , 
            KEY `FK_fre_IdEstado`(`est_intIdEstado`) , 
            KEY `FK_fre_IdCreado`(`usu_intIdCreador`) , 
            KEY `FK_fre_IdActualizador`(`usu_intIdActualizador`) , 
            CONSTRAINT `FK_fre_IdActualizador` 
            FOREIGN KEY (`usu_intIdActualizador`) REFERENCES `tbl_usuario` (`usu_intId`) , 
            CONSTRAINT `FK_fre_IdCreador` 
            FOREIGN KEY (`usu_intIdCreador`) REFERENCES `tbl_usuario` (`usu_intId`) , 
            CONSTRAINT `FK_fre_IdEstado` 
            FOREIGN KEY (`est_intIdEstado`) REFERENCES `tbl_estado` (`est_intId`) 
        ) ENGINE=InnoDB DEFAULT CHARSET='utf8mb3' COLLATE='utf8mb3_general_ci' COMMENT=' * Descripcion: Tabla tbl_frecuencia \r\n * Author: http://www.divergente.net.co\"/> Divergente Soluciones Informaticas S.A.S \r\n * DBA: Jose Perez.\r\n * Created: 2021-10-02 \r\n * Update:\r\n * Fecha Update:\r\n * Version Tabla: 1.0';
        ");
    }
}

