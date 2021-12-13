<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblTipoPersonaMaestro164 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_tipo_persona (
        tpr_intId INT(11) NOT NULL AUTO_INCREMENT,
        tpr_varCodigo VARCHAR(100) NOT NULL,
        tpr_varNombre VARCHAR(100) NOT NULL,
        tpr_lonDescripcion LONGTEXT NOT NULL,
        tpr_varCodigoDian VARCHAR(2) NOT NULL,
        tpr_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
        tpr_dtimFechaActualizacion DATETIME DEFAULT '0000-00-00 00:00:00',
        est_intIdEstado INT(11) NOT NULL DEFAULT 1,
        usu_intIdCreador INT(11) NOT NULL,
        usu_intIdActualizador INT(11)  NULL,
        PRIMARY KEY (tpr_intId)
        )
        ENGINE = INNODB,
        CHARACTER SET utf8,
        COLLATE utf8_general_ci;");

        Model::query("ALTER TABLE tbl_tipo_persona ADD CONSTRAINT FK_tpr_IdEstado FOREIGN KEY (est_intIdEstado)  REFERENCES tbl_estado (est_intId);");
        Model::query("ALTER TABLE tbl_tipo_persona ADD CONSTRAINT FK_tpr_idActualizador FOREIGN KEY (usu_intIdActualizador)  REFERENCES tbl_usuario (usu_intId);");
        Model::query("ALTER TABLE tbl_tipo_persona ADD CONSTRAINT FK_tpr_idCreador FOREIGN KEY (usu_intIdCreador)  REFERENCES tbl_usuario (usu_intId);");


        Model::query("INSERT INTO tbl_tipo_persona 
        (
          tpr_varCodigo,tpr_varNombre,tpr_lonDescripcion,tpr_varCodigoDian,usu_intIdCreador,usu_intIdActualizador
        )
        VALUES 
        (
          'NATURAL','Persona Natural','Persona Natural','2',1,1
        ),
        (
          'JURIDICA','Persona Juridica','Persona Juridica','1',1,1
        );");
    }
}

