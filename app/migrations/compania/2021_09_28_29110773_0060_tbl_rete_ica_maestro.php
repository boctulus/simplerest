<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblRetencionIcaMaestro361 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_rete_ica (
        ric_intId INT(11) NOT NULL AUTO_INCREMENT,
        ric_varReteIca VARCHAR(50) NOT NULL,
        ric_intTope INT(11) NOT NULL,
        ric_intPorcentaje DECIMAL(10, 2) NOT NULL,
        ric_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
        ric_dtimFechaActualizacion DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        est_intIdCidEstado INT(11) NOT NULL DEFAULT 1,
        sub_intIdSubCuentaContable INT(11) DEFAULT NULL,
        usu_intIdCreador INT(11) NOT NULL,
        usu_intIdActualizador INT(11)  NULL,
        PRIMARY KEY (ric_intId)
        )
        ENGINE = INNODB,
        CHARACTER SET utf8,
        COLLATE utf8_general_ci;");

        Model::query("ALTER TABLE tbl_rete_ica 
        ADD CONSTRAINT FK_ric_idEstado FOREIGN KEY (est_intIdCidEstado)
        REFERENCES tbl_estado(est_intId);");

        Model::query("ALTER TABLE tbl_rete_ica 
        ADD CONSTRAINT FK_ric_idActualizador FOREIGN KEY (usu_intIdActualizador)
        REFERENCES tbl_usuario(usu_intId);");

        Model::query("ALTER TABLE tbl_rete_ica 
        ADD CONSTRAINT FK_ric_idCreador FOREIGN KEY (usu_intIdCreador)
        REFERENCES tbl_usuario(usu_intId);
        ");
        
        Model::query("ALTER TABLE tbl_rete_ica 
        ADD CONSTRAINT FK_ric_idSubCuentaContable FOREIGN KEY (sub_intIdSubCuentaContable)
        REFERENCES tbl_sub_cuenta_contable(sub_intId);");
    }
}

