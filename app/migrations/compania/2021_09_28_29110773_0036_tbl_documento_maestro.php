<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblDocumentoMaestro185 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_documento (
        doc_intId INT(11) NOT NULL AUTO_INCREMENT,
        doc_varDocumento VARCHAR(4) NOT NULL,
        doc_varDescripcion VARCHAR(150) NOT NULL,
        doc_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
        doc_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
        est_intIdEstado INT(11) NOT NULL DEFAULT 1,
        tra_intIdTransaccion INT(11) NOT NULL,
        usu_intIdCreador INT(11) NOT NULL,
        usu_intIdActualizador INT(11)  NULL,
        PRIMARY KEY (doc_intId)
        )
        ENGINE = INNODB,
        CHARACTER SET utf8,
        COLLATE utf8_general_ci;");

        Model::query("ALTER TABLE tbl_documento  ADD UNIQUE INDEX doc_varDocumento(doc_varDocumento);");
        Model::query("ALTER TABLE tbl_documento ADD CONSTRAINT FK_doc_IdEstado FOREIGN KEY (est_intIdEstado)  REFERENCES tbl_estado (est_intId);");     
        Model::query("ALTER TABLE tbl_documento  ADD CONSTRAINT FK_doc_idActualizador FOREIGN KEY (usu_intIdActualizador)  REFERENCES tbl_usuario(usu_intId);");
        Model::query("ALTER TABLE tbl_documento  ADD CONSTRAINT FK_doc_idCreador FOREIGN KEY (usu_intIdCreador)  REFERENCES tbl_usuario(usu_intId);");
        Model::query("ALTER TABLE tbl_documento ADD CONSTRAINT FK_doc_idTransaccion FOREIGN KEY (tra_intIdTransaccion) REFERENCES tbl_transaccion(tra_intId);");
          
    }
}

