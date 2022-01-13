<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class TblIvaMaestro258 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_iva (
        iva_intId INT(11) NOT NULL AUTO_INCREMENT,
        iva_varIVA VARCHAR(50) NOT NULL,
        iva_intTope INT(11) NOT NULL,
        iva_decPorcentaje DECIMAL(18, 2) NOT NULL,
        iva_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
        iva_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
        est_intIdEstado INT(11) NOT NULL DEFAULT 1,
        usu_intIdCreador INT(11) NOT NULL,
        usu_intIdActualizador INT(11) NOT NULL,
        sub_intIdCuentaContable INT(11) NOT NULL,
        PRIMARY KEY (iva_intId)
        )
        ENGINE = INNODB,
        CHARACTER SET utf8,
        COLLATE utf8_general_ci;");

        Model::query("ALTER TABLE tbl_iva 
        ADD UNIQUE INDEX iva_varIVA(iva_varIVA);");

        Model::query("ALTER TABLE tbl_iva ADD CONSTRAINT FK_iva_IdEstado FOREIGN KEY (est_intIdEstado)  REFERENCES tbl_estado (est_intId);");
        Model::query("ALTER TABLE tbl_iva ADD CONSTRAINT FK_iva_idActualizador FOREIGN KEY (usu_intIdActualizador)  REFERENCES tbl_usuario (usu_intId);");
        Model::query("ALTER TABLE tbl_iva ADD CONSTRAINT FK_iva_idCreador FOREIGN KEY (usu_intIdCreador)  REFERENCES tbl_usuario (usu_intId);");
        Model::query("ALTER TABLE tbl_iva 
        ADD CONSTRAINT FK_iva_sub_cuenta_contable1 FOREIGN KEY (sub_intIdCuentaContable)
        REFERENCES tbl_sub_cuenta_contable(sub_intId);");
  
    }
}

