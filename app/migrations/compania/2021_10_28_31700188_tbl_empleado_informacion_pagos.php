<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class TblEmpleadoInformacionPagos implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE `tbl_empleado_informacion_pago`(
            `eip_intId` int(11) NOT NULL  auto_increment , 
            `eip_intNumeroCuenta` int(11) NULL  DEFAULT 0 , 
            `eip_dtimFechaCreacion` date NOT NULL  DEFAULT '1000-01-01 00:00:00', 
            `eip_dtimFechaActualizacion` date NULL  ,
            `ban_intId` int(11) NULL  DEFAULT 0 , 
            `tcb_intIdTipoCuenta` int(11) NULL  , 
            `per_intIdPersona` int(11) NULL  , 
            `est_intIdEstado` int(11) NULL  , 
            `usu_intIdCreador` int(11) NULL  , 
            `usu_intIdActualizador` int(11) NULL  , 
            PRIMARY KEY (`eip_intId`) , 
            KEY `FK_eip_idBanco`(`ban_intId`) , 
            KEY `FK_eip_idTipoCuenta`(`tcb_intIdTipoCuenta`) , 
            KEY `FK_eip_idEstado`(`est_intIdEstado`) , 
            KEY `FK_eip_idCreador`(`usu_intIdCreador`) , 
            KEY `FK_eip_idActualizador`(`usu_intIdActualizador`) , 
            KEY `FK_eip_idPersona`(`per_intIdPersona`) , 
            CONSTRAINT `FK_eip_idActualizador` 
            FOREIGN KEY (`usu_intIdActualizador`) REFERENCES `tbl_usuario` (`usu_intId`) ON DELETE NO ACTION ON UPDATE NO ACTION , 
            CONSTRAINT `FK_eip_idBanco` 
            FOREIGN KEY (`ban_intId`) REFERENCES `tbl_banco` (`ban_intId`) ON DELETE NO ACTION ON UPDATE NO ACTION , 
            CONSTRAINT `FK_eip_idCreador` 
            FOREIGN KEY (`usu_intIdCreador`) REFERENCES `tbl_usuario` (`usu_intId`) ON DELETE NO ACTION ON UPDATE NO ACTION , 
            CONSTRAINT `FK_eip_idEstado` 
            FOREIGN KEY (`est_intIdEstado`) REFERENCES `tbl_estado` (`est_intId`) ON DELETE NO ACTION ON UPDATE NO ACTION , 
            CONSTRAINT `FK_eip_idPersona` 
            FOREIGN KEY (`per_intIdPersona`) REFERENCES `tbl_persona` (`per_intId`) ON DELETE NO ACTION ON UPDATE NO ACTION , 
            CONSTRAINT `FK_eip_idTipoCuenta` 
            FOREIGN KEY (`tcb_intIdTipoCuenta`) REFERENCES `tbl_tipo_cuenta_bancaria` (`tcb_intId`) ON DELETE NO ACTION ON UPDATE NO ACTION 
        ) ENGINE=InnoDB DEFAULT CHARSET='armscii8' COLLATE='armscii8_general_ci';");
    }
}

