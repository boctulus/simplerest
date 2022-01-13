<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class TblRhMaestro110 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_rh (
        trh_intId int(11) NOT NULL AUTO_INCREMENT,
        trh_varCodigo varchar(30)  NULL,
        trh_varDescripcion varchar(250) NOT NULL,
        trh_dtimFechaCreacion datetime NOT NULL DEFAULT CURRENT_TIMESTAMP(),
        trh_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
        est_intIdEstado int(11) DEFAULT 1,
        usu_intIdCreador int(11) NOT NULL,
        usu_intIdActualizador int(11)  NULL,
        PRIMARY KEY (trh_intId)
        )
        ENGINE = INNODB,
        CHARACTER SET utf8,
        COLLATE utf8_general_ci,
        COMMENT = ' * Descripcion: Tabla tbl_RH 
        * Author: http://www.divergente.net.co';");

        Model::query("ALTER TABLE tbl_rh ADD CONSTRAINT FK_trh_IdEstado FOREIGN KEY (est_intIdEstado)  REFERENCES tbl_estado (est_intId);");
        Model::query("ALTER TABLE tbl_rh ADD CONSTRAINT FK_trh_idActualizador FOREIGN KEY (usu_intIdActualizador)  REFERENCES tbl_usuario (usu_intId);");
        Model::query("ALTER TABLE tbl_rh ADD CONSTRAINT FK_trh_idCreador FOREIGN KEY (usu_intIdCreador)  REFERENCES tbl_usuario (usu_intId);");


        Model::query("INSERT INTO tbl_rh
        (
           trh_varCodigo, trh_varDescripcion,usu_intIdCreador,usu_intIdActualizador
        )
        VALUES 
        (
          '01', 'O NEGATIVO',1,1
        ),
        (
          '02', 'O POSITIVO',1,1
        ),
        (
          '03', 'A NEGATIVO',1,1
        ),
        (
          '04', 'A POSITIVO',1,1
        ),
        (
          '05', 'B NEGATIVO',1,1
        ),
        (
          '06', 'B POSITIVO',1,1
        ),
        (
          '07', 'AB NEGATIVO',1,1
        )
        ,
        (
          '08', 'AB POSITIVO',1,1
        );"
        );
    }
}

