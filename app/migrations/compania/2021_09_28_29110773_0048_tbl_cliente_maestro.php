<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblClienteMaestro240 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_cliente (
        cli_intId INT(11) NOT NULL AUTO_INCREMENT,
        cli_intDiasGracia INT(11) NOT NULL,
        cli_decCupoCredito DECIMAL(18, 2) NOT NULL,
        cli_bolBloqueadoMora TINYINT(1) NOT NULL,
        cli_datFechaBloqueado DATE NOT NULL,
        cli_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
        cli_dtimFechaActualizacion DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        est_intIdEstado INT(11) NOT NULL DEFAULT 1,
        dpa_intIdDiasPago INT(11) NOT NULL,
        des_intIdDescuento INT(11) NOT NULL,
        per_intIdPersona INT(11) NOT NULL,
        usu_intIdCreador INT(11) DEFAULT NULL,
        usu_intIdActualizador INT(11) DEFAULT NULL,
        PRIMARY KEY (cli_intId)
        )
        ENGINE = INNODB,
        CHARACTER SET utf8,
        COLLATE utf8_general_ci;");

        Model::query("ALTER TABLE tbl_cliente 
        ADD UNIQUE INDEX per_intIdPersona(per_intIdPersona);");

        Model::query("ALTER TABLE tbl_cliente 
        ADD INDEX FK_cli_DiasPago(dpa_intIdDiasPago);");

        Model::query("ALTER TABLE tbl_cliente 
        ADD CONSTRAINT FK_cli_Descuento FOREIGN KEY (des_intIdDescuento)
        REFERENCES tbl_descuento(des_intId);");

        Model::query("ALTER TABLE tbl_cliente 
        ADD CONSTRAINT FK_cli_IdEstado FOREIGN KEY (est_intIdEstado)
        REFERENCES tbl_estado(est_intId);");

        Model::query("ALTER TABLE tbl_cliente 
        ADD CONSTRAINT FK_cli_idPersona FOREIGN KEY (per_intIdPersona)
        REFERENCES tbl_persona(per_intId);");

        Model::query("ALTER TABLE tbl_cliente 
        ADD CONSTRAINT FK_cli1_dias_pago FOREIGN KEY (dpa_intIdDiasPago)
        REFERENCES tbl_dias_pago(dpa_intId);");

        Model::query("ALTER TABLE tbl_cliente 
        ADD CONSTRAINT FK_usu_IdActualizardor FOREIGN KEY (usu_intIdActualizador)
        REFERENCES tbl_usuario(usu_intId);");

        Model::query("ALTER TABLE tbl_cliente 
        ADD CONSTRAINT FK_usu_IdCreador FOREIGN KEY (usu_intIdCreador)
        REFERENCES tbl_usuario(usu_intId);");

        
        
    }
}

