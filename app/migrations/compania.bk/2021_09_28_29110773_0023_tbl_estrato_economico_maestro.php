<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblEstratoEconomicoMaestro115 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_estrato_economico (
        tec_intId int(11) NOT NULL AUTO_INCREMENT,
        tec_varCodigo varchar(20) NOT NULL,
        tec_varDescripcion varchar(250) NOT NULL,
        tec_dtimFechaCreacion datetime NOT NULL DEFAULT CURRENT_TIMESTAMP(),
        tec_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
        est_intIdestado int(11) DEFAULT 1,
        usu_intIdCreador int(11) NOT NULL,
        usu_intIdActualizador int(11)  NULL,
        PRIMARY KEY (tec_intId)
        )
        ENGINE = INNODB,
        CHARACTER SET utf8,
        COLLATE utf8_general_ci,
        COMMENT = ' * Descripcion: Tabla tbl_estrato_economico 
        * Author: http://www.divergente.net.co';");

        Model::query("ALTER TABLE tbl_estrato_economico ADD CONSTRAINT FK_tec_IdEstado FOREIGN KEY (est_intIdEstado)  REFERENCES tbl_estado (est_intId);");
        Model::query("ALTER TABLE tbl_estrato_economico ADD CONSTRAINT FK_tec_idActualizador FOREIGN KEY (usu_intIdActualizador)  REFERENCES tbl_usuario (usu_intId);");
        Model::query("ALTER TABLE tbl_estrato_economico ADD CONSTRAINT FK_tec_idCreador FOREIGN KEY (usu_intIdCreador)  REFERENCES tbl_usuario (usu_intId);");


        Model::query("INSERT INTO tbl_estrato_economico (tec_varCodigo, tec_varDescripcion,  usu_intIdCreador,
        usu_intIdActualizador )
        VALUES ('UNO', 'BAJO-BAJO', 1,1),
        ('DOS', 'BAJO', 1,1),
        ('TRES', 'MEDIO-BAJO', 1,1),
        ('CUATRO', 'MEDIO', 1,1),
        ('CINCO', 'MEDIO-ALTO', 1,1),
        ('SEIS', 'ALTO', 1,1);");
    }
}

