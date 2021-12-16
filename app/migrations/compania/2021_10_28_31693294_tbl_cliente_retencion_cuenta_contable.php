<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblClienteRetencionCuentaContable implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE `tbl_cliente_retencion_cuentacontable`(
            `rcl_intId` int(11) NOT NULL  auto_increment , 
            `rcl_intIdRetencion` int(11) NOT NULL  DEFAULT 0 , 
            `rcl_intIdCuentaContable` int(11) NOT NULL  DEFAULT 0 , 
            `rcl_dtimFechaCreacion` datetime NOT NULL  DEFAULT current_timestamp() , 
            `rcl_dtimFechaActualizacion` datetime NOT NULL  DEFAULT '1000-01-01 00:00:00' , 
            `cli_intIdCliente` int(11) NOT NULL  DEFAULT 0 , 
            `usu_intIdCreador` int(11) NOT NULL  DEFAULT 0 , 
            `usu_intIdActualizador` int(11) NOT NULL  DEFAULT 0 , 
            PRIMARY KEY (`rcl_intId`,`rcl_intIdRetencion`,`rcl_intIdCuentaContable`) , 
            KEY `FK_rcl_intIdCliente`(`cli_intIdCliente`) , 
            KEY `FK_rcl_intIdCuentacontable`(`rcl_intIdCuentaContable`) , 
            KEY `FK_rcl_intIdRetencion`(`rcl_intIdRetencion`) , 
            KEY `FK_rcl_intIdUsuarioActualizador`(`usu_intIdActualizador`) , 
            KEY `FK_rcl_intIdUsuarioCreador`(`usu_intIdCreador`) , 
            CONSTRAINT `FK_rcl_intIdCliente` 
            FOREIGN KEY (`cli_intIdCliente`) REFERENCES `tbl_cliente` (`cli_intId`) , 
            CONSTRAINT `FK_rcl_intIdCuentacontable` 
            FOREIGN KEY (`rcl_intIdCuentaContable`) REFERENCES `tbl_sub_cuenta_contable` (`sub_intId`) , 
            CONSTRAINT `FK_rcl_intIdRetencion` 
            FOREIGN KEY (`rcl_intIdRetencion`) REFERENCES `tbl_retencion` (`ret_intId`) , 
            CONSTRAINT `FK_rcl_intIdUsuarioActualizador` 
            FOREIGN KEY (`usu_intIdActualizador`) REFERENCES `tbl_usuario` (`usu_intId`) , 
            CONSTRAINT `FK_rcl_intIdUsuarioCreador` 
            FOREIGN KEY (`usu_intIdCreador`) REFERENCES `tbl_usuario` (`usu_intId`) 
        ) ENGINE=InnoDB DEFAULT CHARSET='utf8mb3' COLLATE='utf8mb3_general_ci';");
    }
}

