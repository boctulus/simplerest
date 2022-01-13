<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;


class TblPreferenciasMaestro381 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {

        Model::query("CREATE TABLE tbl_preferencias (
        tpf_intId INT(11) NOT NULL AUTO_INCREMENT,
        tpf_varCodigo VARCHAR(50) NOT NULL,
        tpf_varNombre VARCHAR(250) NOT NULL,
        tpf_lonDescripcion LONGTEXT NOT NULL,
        tpf_bitUso BIGINT(20) NOT NULL DEFAULT 0,
        tpf_varParametro VARCHAR(50) NOT NULL,
        tpf_varTipoDato VARCHAR(50) NOT NULL,
        tpf_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
        tpf_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
        doc_intIdDocumento INT(11) NOT NULL DEFAULT 38,
        men_idId INT(11) NOT NULL DEFAULT 44,
        est_intIdEstado INT(11) NOT NULL DEFAULT 1,
        usu_intIdCreador INT(11) NOT NULL,
        usu_intIdActualizador INT(11)  NULL,
        PRIMARY KEY (tpf_intId)
        )
        ENGINE = INNODB,
        CHARACTER SET utf8,
        COLLATE utf8_general_ci,
        COMMENT = ' * Descripcion: Tabla tbl_preferencias 
        * Author: http://www.divergente.net.co';");

        Model::query("ALTER TABLE tbl_preferencias 
        ADD CONSTRAINT FK_tpf_IdActualizador FOREIGN KEY (usu_intIdActualizador)
        REFERENCES tbl_usuario(usu_intId);
        ");

        Model::query("ALTER TABLE tbl_preferencias 
        ADD CONSTRAINT FK_tpf_IdCreador FOREIGN KEY (usu_intIdCreador)
        REFERENCES tbl_usuario(usu_intId);
        ");

        Model::query("ALTER TABLE tbl_preferencias 
        ADD CONSTRAINT FK_tpf_IdDocumento FOREIGN KEY (doc_intIdDocumento)
        REFERENCES tbl_documento(doc_intId);
        ");

        Model::query("ALTER TABLE tbl_preferencias 
        ADD CONSTRAINT FK_tpf_IdEstado FOREIGN KEY (est_intIdEstado)
        REFERENCES tbl_estado(est_intId);
        ");

        
    }
}

