<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class TblReteiva implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE `tbl_reteiva`(
            `riv_intId` int(11) NOT NULL  auto_increment , 
            `riv_varReteIva` varchar(50) COLLATE utf8mb3_general_ci NOT NULL  , 
            `riv_intTope` int(11) NOT NULL  , 
            `riv_decPorcentaje` decimal(10,2) NOT NULL  , 
            `riv_dtimFechaCreacion` datetime NOT NULL  DEFAULT current_timestamp() , 
            `riv_dtimFechaActualizacion` datetime NOT NULL  DEFAULT '1000-01-01 00:00:00' on update current_timestamp() , 
            `est_intIdEstado` int(11) NOT NULL  DEFAULT 1 , 
            `sub_intIdsubcuentacontable` int(11) NULL  , 
            `usu_intIdCreador` int(11) NOT NULL  , 
            `usu_intIdActualizador` int(11) NOT NULL  , 
            PRIMARY KEY (`riv_intId`) , 
            UNIQUE KEY `riv_varReteIva`(`riv_varReteIva`) , 
            KEY `FK_riv_Idsubcuentacontable`(`sub_intIdsubcuentacontable`) , 
            KEY `FK_riv_idActualizador`(`usu_intIdActualizador`) , 
            KEY `FK_riv_idCreador`(`usu_intIdCreador`) , 
            KEY `FK_riv_idEstado`(`est_intIdEstado`) , 
            CONSTRAINT `FK_riv_Idsubcuentacontable` 
            FOREIGN KEY (`sub_intIdsubcuentacontable`) REFERENCES `tbl_sub_cuenta_contable` (`sub_intId`) , 
            CONSTRAINT `FK_riv_idActualizador` 
            FOREIGN KEY (`usu_intIdActualizador`) REFERENCES `tbl_usuario` (`usu_intId`) , 
            CONSTRAINT `FK_riv_idCreador` 
            FOREIGN KEY (`usu_intIdCreador`) REFERENCES `tbl_usuario` (`usu_intId`) , 
            CONSTRAINT `FK_riv_idEstado` 
            FOREIGN KEY (`est_intIdEstado`) REFERENCES `tbl_estado` (`est_intId`) ON DELETE NO ACTION ON UPDATE NO ACTION 
        ) ENGINE=InnoDB DEFAULT CHARSET='utf8mb3' COLLATE='utf8mb3_general_ci';");
    }
}

