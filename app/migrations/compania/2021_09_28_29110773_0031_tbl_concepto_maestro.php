<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblConceptoMaestro155 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_concepto (
        cct_intId int(11) NOT NULL AUTO_INCREMENT,
        cct_varNombre varchar(50) NOT NULL,
        cct_varDescripcion varchar(250) NOT NULL,
        cct_dtimFechaCreacion datetime NOT NULL DEFAULT CURRENT_TIMESTAMP(),
        cct_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
        est_intIdEstado int(11) NOT NULL DEFAULT 1,
        usu_intIdCreador int(11) NOT NULL,
        usu_intIdActualizador int(11) NOT NULL,
        PRIMARY KEY (cct_intId)
        )
        ENGINE = INNODB,
        CHARACTER SET utf8,
        COLLATE utf8_general_ci,
        COMMENT = ' * Descripcion: Tabla tbl_concepto 
        * Author: http://www.divergente.net.co';");

        Model::query("ALTER TABLE tbl_concepto ADD CONSTRAINT FK_cct_IdEstado FOREIGN KEY (est_intIdEstado)  REFERENCES tbl_estado (est_intId);");
        Model::query("ALTER TABLE tbl_concepto ADD CONSTRAINT FK_cct_idActualizador FOREIGN KEY (usu_intIdActualizador)  REFERENCES tbl_usuario (usu_intId);");
        Model::query("ALTER TABLE tbl_concepto ADD CONSTRAINT FK_cct_idCreador FOREIGN KEY (usu_intIdCreador)  REFERENCES tbl_usuario (usu_intId);");

    }
}
