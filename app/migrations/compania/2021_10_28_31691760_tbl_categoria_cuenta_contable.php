<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class TblCategoriaCuentaContable implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE `tbl_categoria_cuenta_contable`(
            `ccc_intId` int(11) NOT NULL  auto_increment , 
            `ccc_varCategoriaCuentaContable` varchar(50) COLLATE utf8mb3_general_ci NOT NULL  , 
            `ccc_dtimFechaCreacion` datetime NOT NULL  DEFAULT current_timestamp(), 
            `ccc_dtimFechaActualizacion` datetime NOT NULL  DEFAULT '1000-01-01 00:00:00' , 
            `est_intIdEstado` int(11) NOT NULL  DEFAULT 1 , 
            `usu_intIdCreador` int(11) NOT NULL  DEFAULT 1 , 
            `usu_intIdActualizador` int(11) NOT NULL  DEFAULT 1 , 
            PRIMARY KEY (`ccc_intId`) , 
            KEY `FK_ccc_IdActualizador`(`usu_intIdActualizador`) , 
            KEY `FK_ccc_IdCreador`(`usu_intIdCreador`) , 
            KEY `FK_ccc_IdEstado`(`est_intIdEstado`) , 
            CONSTRAINT `FK_ccc_IdActualizador` 
            FOREIGN KEY (`usu_intIdActualizador`) REFERENCES `tbl_usuario` (`usu_intId`) , 
            CONSTRAINT `FK_ccc_IdCreador` 
            FOREIGN KEY (`usu_intIdCreador`) REFERENCES `tbl_usuario` (`usu_intId`) , 
            CONSTRAINT `FK_ccc_IdEstado` 
            FOREIGN KEY (`est_intIdEstado`) REFERENCES `tbl_estado` (`est_intId`) 
        ) ENGINE=InnoDB DEFAULT CHARSET='utf8mb3' COLLATE='utf8mb3_general_ci';
        ");

        Model::query("
        INSERT INTO tbl_categoria_cuenta_contable (ccc_varCategoriaCuentaContable) 
        VALUES ('Mayor'),('Auxiliar') ");
    }
}

