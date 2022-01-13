<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class TblClienteReteivaCuentaContable implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE `tbl_cliente_reteiva_cuentacontable`(
            `ric_intId` int(11) NOT NULL  auto_increment , 
            `ric_intIdReteiva` int(11) NOT NULL  DEFAULT 0 , 
            `ric_intIdCuentacontable` int(11) NOT NULL  DEFAULT 0 , 
            `ric_dtimFechaCreacion` datetime NOT NULL  DEFAULT current_timestamp() , 
            `ric_dtimFechaActualizacion` datetime NOT NULL  DEFAULT current_timestamp() , 
            `cli_intIdCliente` int(11) NOT NULL  DEFAULT 0 , 
            `usu_intIdCreador` int(11) NOT NULL  DEFAULT 0 , 
            `usu_intIdActualizador` int(11) NOT NULL  DEFAULT 0 , 
            PRIMARY KEY (`ric_intId`,`ric_intIdReteiva`,`ric_intIdCuentacontable`) , 
            KEY `FK_cli_intIdCliente`(`cli_intIdCliente`) , 
            KEY `FK_ric_intIdCuentaContable`(`ric_intIdCuentacontable`) , 
            KEY `FK_ric_intIdReteIva`(`ric_intIdReteiva`) , 
            CONSTRAINT `FK_cli_intIdCliente` 
            FOREIGN KEY (`cli_intIdCliente`) REFERENCES `tbl_cliente` (`cli_intId`) , 
            CONSTRAINT `FK_ric_intIdCuentaContable` 
            FOREIGN KEY (`ric_intIdCuentacontable`) REFERENCES `tbl_sub_cuenta_contable` (`sub_intId`) , 
            CONSTRAINT `FK_ric_intIdReteIva` 
            FOREIGN KEY (`ric_intIdReteiva`) REFERENCES `tbl_reteiva` (`riv_intId`) 
        ) ENGINE=InnoDB DEFAULT CHARSET='utf8mb3' COLLATE='utf8mb3_general_ci';");
    }
}

