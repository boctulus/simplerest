<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblNaturalezaCuentaContable implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE `tbl_naturaleza_cuenta_contable`(
            `ncc_intId` int(11) NOT NULL  auto_increment , 
            `ncc_varNaturalezaCuenta` varchar(50) COLLATE utf8mb3_general_ci NOT NULL  , 
            `ncc_dtimFechaCreacion` datetime NOT NULL  DEFAULT current_timestamp() , 
            `ncc_dtimFechaActualizacion` datetime NOT NULL  DEFAULT '0000-00-00 00:00:00' , 
            `est_intIdEstado` int(11) NOT NULL  DEFAULT 1 , 
            `usu_intIdCreador` int(11) NOT NULL DEFAULT 1, 
            `usu_intIdActualizador` int(11) NOT NULL DEFAULT 1, 
            PRIMARY KEY (`ncc_intId`) , 
            KEY `FK_ncc_IdEstado`(`est_intIdEstado`) , 
            KEY `FK_ncc_IdCreado`(`usu_intIdCreador`) , 
            KEY `FK_ncc_IdActualizador`(`usu_intIdActualizador`) , 
            CONSTRAINT `FK_ncc_IdActualizador` 
            FOREIGN KEY (`usu_intIdActualizador`) REFERENCES `tbl_usuario` (`usu_intId`) , 
            CONSTRAINT `FK_ncc_IdCreador` 
            FOREIGN KEY (`usu_intIdCreador`) REFERENCES `tbl_usuario` (`usu_intId`) , 
            CONSTRAINT `FK_ncc_IdEstado` 
            FOREIGN KEY (`est_intIdEstado`) REFERENCES `tbl_estado` (`est_intId`) 
        ) ENGINE=InnoDB DEFAULT CHARSET='utf8mb3' COLLATE='utf8mb3_general_ci' COMMENT=' * Descripcion: Tabla tbl_naturaleza_cuenta_contable \r\n * Author: http://www.divergente.net.co\"/> Divergente Soluciones Informaticas S.A.S \r\n * DBA: Jose Perez.\r\n * Created: 2021-10-02 \r\n * Update:\r\n * Fecha Update:\r\n * Version Tabla: 1.0';        
        ");

        Model::query("INSERT INTO tbl_naturaleza_cuenta_contable(ncc_varNaturalezaCuenta) VALUES ('Credito'),('Debito')");
    }
}

