<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class TblBancoMaestro271 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_banco (
        ban_intId INT(11) NOT NULL AUTO_INCREMENT,
        ban_varCodigo VARCHAR(100)  NULL,
        ban_varNombre VARCHAR(100) NOT NULL,
        ban_lonDescripcion LONGTEXT  NULL,
        ban_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
        ban_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
        est_intIdEstado INT(11) NOT NULL DEFAULT 1,
        usu_intIdCreador INT(11) NOT NULL,
        usu_intIdActualizador INT(11)  NULL,
        sub_intIdCuentaCxC INT(11) NOT NULL,
        PRIMARY KEY (ban_intId)
        )
        ENGINE = INNODB,
        CHARACTER SET utf8,
        COLLATE utf8_general_ci;");

        Model::query("ALTER TABLE tbl_banco 
        ADD CONSTRAINT FK_ban_IdActualizador FOREIGN KEY (usu_intIdActualizador)
        REFERENCES tbl_usuario(usu_intId);");

        Model::query("ALTER TABLE tbl_banco 
        ADD CONSTRAINT FK_ban_IdCreador FOREIGN KEY (usu_intIdCreador)
        REFERENCES tbl_usuario(usu_intId);");
        
        Model::query("ALTER TABLE tbl_banco 
        ADD CONSTRAINT FK_ban_IdEstado FOREIGN KEY (est_intIdEstado)
          REFERENCES tbl_estado(est_intId);
        ");

        Model::query("ALTER TABLE tbl_banco 
        ADD CONSTRAINT FK_banco_sub_cuenta_contable FOREIGN KEY (sub_intIdCuentaCxC)
        REFERENCES tbl_sub_cuenta_contable(sub_intId);
        ");
    }
}

