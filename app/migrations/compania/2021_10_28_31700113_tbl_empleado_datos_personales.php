<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblEmpleadoDatosPersonales implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE `tbl_empleado_datos_personales`(
            `edp_intId` int(11) NOT NULL  auto_increment , 
            `epd_decEstatura` decimal(18,2) NULL  DEFAULT 0.00 , 
            `epd_decPeso` decimal(18,2) NULL  DEFAULT 0.00 , 
            `epd_intNumeroLibretaMilitar` int(11) NULL  , 
            `epd_varMotivoNoPrestoServicio` varchar(350) COLLATE armscii8_general_ci NULL  , 
            `epd_intNumeroLicencia` int(11) NULL  , 
            `epd_dtimFechaCreacion` date NOT NULL  DEFAULT current_timestamp(), 
            `epd_dtimFechaActualizacion` date NULL  , 
            `esc_intIdEstadoCivil` int(11) NULL  , 
            `per_intIdPersona` int(11) NULL  , 
            `esd_intIdEstudios` int(11) NULL  , 
            `trh_intIdRH` int(11) NULL  , 
            `clm_intIdClaseLibretaMilitar` int(11) NULL  , 
            `clc_intIdCategoriaLicencia` int(11) NULL  , 
            `est_intIdEstado` int(11) NULL  , 
            `usu_intIdCreador` int(11) NULL  , 
            `usu_intIdActualizador` int(11) NULL  , 
            PRIMARY KEY (`edp_intId`) , 
            KEY `FK_epd_idEstadoCivil`(`esc_intIdEstadoCivil`) , 
            KEY `FK_epd_idEstudios`(`esd_intIdEstudios`) , 
            KEY `FK_epd_idRH`(`trh_intIdRH`) , 
            KEY `FK_epd_idClaseLM`(`clm_intIdClaseLibretaMilitar`) , 
            KEY `FK_epd_idCategoriaLicencia`(`clc_intIdCategoriaLicencia`) , 
            KEY `FK_epd_idEstado`(`est_intIdEstado`) , 
            KEY `FK_epd_idCreador`(`usu_intIdCreador`) , 
            KEY `FK_epd_idActualizador`(`usu_intIdActualizador`) , 
            KEY `FK_epd_idPersona`(`per_intIdPersona`) , 
            CONSTRAINT `FK_epd_idActualizador` 
            FOREIGN KEY (`usu_intIdActualizador`) REFERENCES `tbl_usuario` (`usu_intId`) ON DELETE NO ACTION ON UPDATE NO ACTION , 
            CONSTRAINT `FK_epd_idCategoriaLicencia` 
            FOREIGN KEY (`clc_intIdCategoriaLicencia`) REFERENCES `tbl_categoria_licencia_conduccion` (`clc_intId`) ON DELETE NO ACTION ON UPDATE NO ACTION , 
            CONSTRAINT `FK_epd_idClaseLM` 
            FOREIGN KEY (`clm_intIdClaseLibretaMilitar`) REFERENCES `tbl_clase_libreta_militar` (`clm_intId`) ON DELETE NO ACTION ON UPDATE NO ACTION , 
            CONSTRAINT `FK_epd_idCreador` 
            FOREIGN KEY (`usu_intIdCreador`) REFERENCES `tbl_usuario` (`usu_intId`) ON DELETE NO ACTION ON UPDATE NO ACTION , 
            CONSTRAINT `FK_epd_idEstado` 
            FOREIGN KEY (`est_intIdEstado`) REFERENCES `tbl_estado` (`est_intId`) ON DELETE NO ACTION ON UPDATE NO ACTION , 
            CONSTRAINT `FK_epd_idEstadoCivil` 
            FOREIGN KEY (`esc_intIdEstadoCivil`) REFERENCES `tbl_estado_civil` (`esc_intId`) ON DELETE NO ACTION ON UPDATE NO ACTION , 
            CONSTRAINT `FK_epd_idEstudios` 
            FOREIGN KEY (`esd_intIdEstudios`) REFERENCES `tbl_estudios` (`esd_intId`) ON DELETE NO ACTION ON UPDATE NO ACTION , 
            CONSTRAINT `FK_epd_idPersona` 
            FOREIGN KEY (`per_intIdPersona`) REFERENCES `tbl_persona` (`per_intId`) ON DELETE NO ACTION ON UPDATE NO ACTION , 
            CONSTRAINT `FK_epd_idRH` 
            FOREIGN KEY (`trh_intIdRH`) REFERENCES `tbl_rh` (`trh_intId`) ON DELETE NO ACTION ON UPDATE NO ACTION 
        ) ENGINE=InnoDB DEFAULT CHARSET='armscii8' COLLATE='armscii8_general_ci';");
    }
}

