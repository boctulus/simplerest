<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblConsecutivoMaestro194 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_consecutivo (
        cse_intId INT(11) NOT NULL AUTO_INCREMENT,
        cse_intConsecutivo INT(11) NOT NULL DEFAULT 0,
        cse_varPrefijo VARCHAR(4) DEFAULT NULL,
        cse_intDesde INT(11) NOT NULL,
        cse_intHasta INT(11) NOT NULL,
        cse_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
        cse_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
        doc_intIdDocumento INT(11) NOT NULL,
        res_intIdResolucion INT(11) NOT NULL,
        est_intIdEstado INT(11) NOT NULL DEFAULT 1 ,
        usu_intIdCreador INT(11) NOT NULL,
        usu_intIdActualizador INT(11)  NULL,
        PRIMARY KEY (cse_intId)
        )
        ENGINE = INNODB,
        CHARACTER SET utf8,
        COLLATE utf8_general_ci;");

        Model::query("ALTER TABLE tbl_consecutivo ADD CONSTRAINT FK_cse_IdEstado FOREIGN KEY (est_intIdEstado)  REFERENCES tbl_estado (est_intId);");     

        Model::query("ALTER TABLE tbl_consecutivo 
        ADD CONSTRAINT FK_cse_idActualizador FOREIGN KEY (usu_intIdActualizador)
        REFERENCES tbl_usuario(usu_intId);");

        Model::query("ALTER TABLE tbl_consecutivo 
        ADD CONSTRAINT FK_cse_idCreador FOREIGN KEY (usu_intIdCreador)
        REFERENCES tbl_usuario(usu_intId);");

        Model::query("ALTER TABLE tbl_consecutivo 
        ADD CONSTRAINT FK_cse_idDocumento FOREIGN KEY (doc_intIdDocumento)
        REFERENCES tbl_documento(doc_intId);");

        Model::query("ALTER TABLE tbl_consecutivo 
        ADD CONSTRAINT FK_cse_idResolucion FOREIGN KEY (res_intIdResolucion)
        REFERENCES tbl_resolucion(res_intId);");
    }
}

