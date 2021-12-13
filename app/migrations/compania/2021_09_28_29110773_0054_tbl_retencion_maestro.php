<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblRetencionMaestro301 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_retencion (
        ret_intId INT(11) NOT NULL AUTO_INCREMENT,
        ret_varRetencion VARCHAR(50) NOT NULL,
        ret_intTope INT(11) NOT NULL,
        ret_decPorcentaje DECIMAL(10, 2) NOT NULL,
        ret_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
        ret_dtimFechaActualizacion DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        est_intIdEstado INT(11) NOT NULL DEFAULT 1,
        usu_intIdCreador INT(11) NOT NULL,
        usu_intIdActualizador INT(11) NOT NULL,
        sub_intIdCuentaContable INT(11) DEFAULT NULL,
        PRIMARY KEY (ret_intId)
        )
        ENGINE = INNODB,
        CHARACTER SET utf8,
        COLLATE utf8_general_ci;
        ");

        Model::query("ALTER TABLE tbl_retencion ADD CONSTRAINT FK_ret_IdEstado FOREIGN KEY (est_intIdEstado)  REFERENCES tbl_estado (est_intId);");
        Model::query("ALTER TABLE tbl_retencion ADD CONSTRAINT FK_ret_idActualizador FOREIGN KEY (usu_intIdActualizador)  REFERENCES tbl_usuario (usu_intId);");
        Model::query("ALTER TABLE tbl_retencion ADD CONSTRAINT FK_ret_idCreador FOREIGN KEY (usu_intIdCreador)  REFERENCES tbl_usuario (usu_intId);");
        Model::query("ALTER TABLE tbl_retencion 
        ADD CONSTRAINT FK_ret_sub_cuenta_contable FOREIGN KEY (sub_intIdCuentaContable)
        REFERENCES tbl_sub_cuenta_contable(sub_intId);");
    }
}

