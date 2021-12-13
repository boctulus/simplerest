<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblTransaccionMaestro181 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_transaccion (
        tra_intId INT(11) NOT NULL AUTO_INCREMENT,
        tra_varTransaccion VARCHAR(25) NOT NULL,
        tra_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
        tra_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
        est_intIdEstado INT(11) NOT NULL DEFAULT 1,
        usu_intIdCreador INT(11) NOT NULL,
        usu_intIdActualizador INT(11)  NULL,
        PRIMARY KEY (tra_intId)
        )
        ENGINE = INNODB,
        CHARACTER SET utf8,
        COLLATE utf8_general_ci;
        ");

        Model::query("ALTER TABLE tbl_transaccion ADD UNIQUE INDEX tra_varTransaccion(tra_varTransaccion);");
        Model::query("ALTER TABLE tbl_transaccion ADD CONSTRAINT FK_tra_IdEstado FOREIGN KEY (est_intIdEstado)  REFERENCES tbl_estado (est_intId);");     
        Model::query("ALTER TABLE tbl_transaccion ADD CONSTRAINT FK_tra_idActualizador FOREIGN KEY (usu_intIdActualizador) REFERENCES tbl_usuario(usu_intId);");
        Model::query("ALTER TABLE tbl_transaccion ADD CONSTRAINT FK_tra_idCreador FOREIGN KEY (usu_intIdCreador)  REFERENCES tbl_usuario(usu_intId);");
        
        
    }
}

