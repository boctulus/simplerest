<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblEmpleadoDatosGenerales implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE `tbl_empleado_datos_generales`(
            `edg_intId` int(11) NOT NULL  auto_increment , 
            `edg_datFechaExpCedula` date NULL  , 
            `edg_varVivienda` varchar(250) COLLATE armscii8_general_ci NULL  , 
            `edg_varLugarResidencia` varchar(250) COLLATE armscii8_general_ci NULL  , 
            `edg_varDireccion` varchar(350) COLLATE armscii8_general_ci NULL  , 
            `edg_varTipoVia` varchar(200) COLLATE armscii8_general_ci NULL  , 
            `edg_varNumero` varchar(200) COLLATE armscii8_general_ci NULL  , 
            `edg_varLetra` varchar(200) COLLATE armscii8_general_ci NULL  , 
            `edg_varCuadrante` varchar(200) COLLATE armscii8_general_ci NULL  , 
            `edg_intTelefono` int(11) NULL  , 
            `edg_intMovil` int(11) NULL  , 
            `edg_varEmail` varchar(250) COLLATE armscii8_general_ci NULL  , 
            `edg_lonNota` longtext COLLATE armscii8_general_ci NULL  , 
            `epg_dtimFechaCreacion` date NOT NULL  DEFAULT '1000-01-01' , 
            `epg_dtimFechaActualizacion` date NULL  , 
            `pai_intIdPaisExpCedula` int(11) NULL  , 
            `per_intIdPersona` int(11) NULL  , 
            `ciu_intIdCiudadExpCedula` int(11) NULL  , 
            `dep_intIdDepartaExpCedula` int(11) NULL  , 
            `pai_intIdPais` int(11) NULL  , 
            `ciu_intIdCiudad` int(11) NULL  , 
            `dep_intIdDepartamento` int(11) NULL  , 
            `est_intIdEstado` int(11) NULL  , 
            `usu_intIdCreador` int(11) NULL  , 
            `usu_intIdActualizador` int(11) NULL  , 
            PRIMARY KEY (`edg_intId`) , 
            KEY `FK_epg_idPasiExpCedula`(`pai_intIdPaisExpCedula`) , 
            KEY `FK_epg_idCiuExpCedula`(`ciu_intIdCiudadExpCedula`) , 
            KEY `FK_epg_idDeparExpCedula`(`dep_intIdDepartaExpCedula`) , 
            KEY `FK_epg_idPais`(`pai_intIdPais`) , 
            KEY `FK_epg_idCiudad`(`ciu_intIdCiudad`) , 
            KEY `FK_epg_idDepartamento`(`dep_intIdDepartamento`) , 
            KEY `FK_epg_idEstado`(`est_intIdEstado`) , 
            KEY `FK_epg_idCreador`(`usu_intIdCreador`) , 
            KEY `FK_epg_idActualizador`(`usu_intIdActualizador`) , 
            KEY `FK_epg_idPersona`(`per_intIdPersona`) , 
            CONSTRAINT `FK_epg_idActualizador` 
            FOREIGN KEY (`usu_intIdActualizador`) REFERENCES `tbl_usuario` (`usu_intId`) ON DELETE NO ACTION ON UPDATE NO ACTION , 
            CONSTRAINT `FK_epg_idCiuExpCedula` 
            FOREIGN KEY (`ciu_intIdCiudadExpCedula`) REFERENCES `tbl_ciudad` (`ciu_intId`) ON DELETE NO ACTION ON UPDATE NO ACTION , 
            CONSTRAINT `FK_epg_idCiudad` 
            FOREIGN KEY (`ciu_intIdCiudad`) REFERENCES `tbl_ciudad` (`ciu_intId`) ON DELETE NO ACTION ON UPDATE NO ACTION , 
            CONSTRAINT `FK_epg_idCreador` 
            FOREIGN KEY (`usu_intIdCreador`) REFERENCES `tbl_usuario` (`usu_intId`) ON DELETE NO ACTION ON UPDATE NO ACTION , 
            CONSTRAINT `FK_epg_idDeparExpCedula` 
            FOREIGN KEY (`dep_intIdDepartaExpCedula`) REFERENCES `tbl_departamento` (`dep_intId`) ON DELETE NO ACTION ON UPDATE NO ACTION , 
            CONSTRAINT `FK_epg_idDepartamento` 
            FOREIGN KEY (`dep_intIdDepartamento`) REFERENCES `tbl_departamento` (`dep_intId`) ON DELETE NO ACTION ON UPDATE NO ACTION , 
            CONSTRAINT `FK_epg_idEstado` 
            FOREIGN KEY (`est_intIdEstado`) REFERENCES `tbl_estado` (`est_intId`) ON DELETE NO ACTION ON UPDATE NO ACTION , 
            CONSTRAINT `FK_epg_idPais` 
            FOREIGN KEY (`pai_intIdPais`) REFERENCES `tbl_pais` (`pai_intId`) ON DELETE NO ACTION ON UPDATE NO ACTION , 
            CONSTRAINT `FK_epg_idPasiExpCedula` 
            FOREIGN KEY (`pai_intIdPaisExpCedula`) REFERENCES `tbl_pais` (`pai_intId`) ON DELETE NO ACTION ON UPDATE NO ACTION , 
            CONSTRAINT `FK_epg_idPersona` 
            FOREIGN KEY (`per_intIdPersona`) REFERENCES `tbl_persona` (`per_intId`) ON DELETE NO ACTION ON UPDATE NO ACTION 
        ) ENGINE=InnoDB DEFAULT CHARSET='armscii8' COLLATE='armscii8_general_ci';");
    }
}

