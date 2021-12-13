<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblInformacionTributaria implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE `tbl_informacion_tributaria`(
            `tft_intId` int(11) NOT NULL  auto_increment , 
            `tft_bolGrancontribuyente` tinyint(4) NOT NULL  DEFAULT 0 , 
            `tft_bolLLevarContabilidad` tinyint(4) NOT NULL  DEFAULT 0 , 
            `tft_bolCalculaIca` tinyint(4) NOT NULL  DEFAULT 0 , 
            `tft_dtimFechaCreacion` datetime NOT NULL  DEFAULT current_timestamp() , 
            `tft_dtimFechaActualizacion` datetime NOT NULL  DEFAULT '0000-00-00 00:00:00' , 
            `per_intIdpersona` int(11) NULL  , 
            `sub_intIdcxp_subcuentacontable` int(11) NULL  , 
            `sub_intIdcxc_subcuentacontable` int(11) NULL  , 
            `est_intIdEstado` int(11) NOT NULL  DEFAULT 1 , 
            `usu_intIdCreador` int(11) NOT NULL  , 
            `usu_intIdActualizador` int(11) NOT NULL  , 
            PRIMARY KEY (`tft_intId`) , 
            UNIQUE KEY `per_intIdpersona`(`per_intIdpersona`) , 
            KEY `FK_tft_IdCreado`(`usu_intIdCreador`) , 
            KEY `FK_tft_IdActualizador`(`usu_intIdActualizador`) , 
            KEY `FK_tft_IdEstado`(`est_intIdEstado`) , 
            KEY `FK_tft_Idcxc_subcuentacontable`(`sub_intIdcxc_subcuentacontable`) , 
            KEY `FK_tft_Idcxp_subcuentacontable`(`sub_intIdcxp_subcuentacontable`) , 
            CONSTRAINT `FK_tft_IdActualizador` 
            FOREIGN KEY (`usu_intIdActualizador`) REFERENCES `tbl_usuario` (`usu_intId`) , 
            CONSTRAINT `FK_tft_IdCreador` 
            FOREIGN KEY (`usu_intIdCreador`) REFERENCES `tbl_usuario` (`usu_intId`) , 
            CONSTRAINT `FK_tft_IdEstado` 
            FOREIGN KEY (`est_intIdEstado`) REFERENCES `tbl_estado` (`est_intId`) , 
            CONSTRAINT `FK_tft_IdPersona` 
            FOREIGN KEY (`per_intIdpersona`) REFERENCES `tbl_persona` (`per_intId`) , 
            CONSTRAINT `FK_tft_Idcxc_subcuentacontable` 
            FOREIGN KEY (`sub_intIdcxc_subcuentacontable`) REFERENCES `tbl_sub_cuenta_contable` (`sub_intId`) , 
            CONSTRAINT `FK_tft_Idcxp_subcuentacontable` 
            FOREIGN KEY (`sub_intIdcxp_subcuentacontable`) REFERENCES `tbl_sub_cuenta_contable` (`sub_intId`) 
        ) ENGINE=InnoDB DEFAULT CHARSET='utf8mb3' COLLATE='utf8mb3_general_ci' COMMENT=' * Descripcion: Tabla tbl_informacion_tributaria \r\n * Author: http://www.divergente.net.co\"/> Divergente Soluciones Informaticas S.A.S \r\n * DBA: Jose Perez.\r\n * Created: 2021-09-16 \r\n * Update:\r\n * Fecha Update:\r\n * Version Tabla: 1.0';
        
        ");
    }
}

