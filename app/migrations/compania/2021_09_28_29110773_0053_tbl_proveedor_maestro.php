<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblProveedorMaestro284 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_proveedor (
        prv_intId INT(11) NOT NULL AUTO_INCREMENT,
        prv_varCuentaBancaria VARCHAR(15) NOT NULL,
        prv_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
        prv_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
        dpa_intIdDiasPago INT(11) NOT NULL,
        ban_intIdBanco INT(11) NOT NULL,
        ccb_intIdCategoriaCuentaBancaria INT(11) NOT NULL,
        per_intIdPersona INT(11) DEFAULT NULL,
        est_intIdEstado INT(11) DEFAULT 1,
        usu_intIdCreador INT(11) DEFAULT NULL,
        usu_intIdActualizador INT(11) DEFAULT NULL,
        PRIMARY KEY (prv_intId)
        )
        ENGINE = INNODB,
        CHARACTER SET utf8,
        COLLATE utf8_general_ci;
        ");

        Model::query("ALTER TABLE tbl_proveedor 
        ADD INDEX FK_prv_DiasPago(dpa_intIdDiasPago);
        ");

        Model::query("ALTER TABLE tbl_proveedor 
        ADD INDEX FK_prv_idCategoriaCuentaBancaria(ccb_intIdCategoriaCuentaBancaria);
        ");

        Model::query("ALTER TABLE tbl_proveedor 
        ADD CONSTRAINT FK_prv_idBanco FOREIGN KEY (ban_intIdBanco)
        REFERENCES tbl_banco(ban_intId);
        ");

        Model::query("ALTER TABLE tbl_proveedor 
        ADD CONSTRAINT FK_prv_idEstadoPersona FOREIGN KEY (est_intIdEstado)
        REFERENCES tbl_estado(est_intId);
        ");

        Model::query("ALTER TABLE tbl_proveedor 
        ADD CONSTRAINT FK_prv_idPersona FOREIGN KEY (per_intIdPersona)
        REFERENCES tbl_persona(per_intId);
        ");

        Model::query("ALTER TABLE tbl_proveedor 
        ADD CONSTRAINT FK_proveedor_categoria_cuenta_bancaria FOREIGN KEY (ccb_intIdCategoriaCuentaBancaria)
        REFERENCES tbl_categoria_cuenta_bancaria(ccb_intId);
        ");

        Model::query("ALTER TABLE tbl_proveedor 
        ADD CONSTRAINT FK_proveedor_dias_pago FOREIGN KEY (dpa_intIdDiasPago)
        REFERENCES tbl_dias_pago(dpa_intId);
        ");

        Model::query("ALTER TABLE tbl_proveedor 
        ADD CONSTRAINT FK_usuIdActualizador FOREIGN KEY (usu_intIdActualizador)
        REFERENCES tbl_usuario(usu_intId);
        ");

        Model::query("ALTER TABLE tbl_proveedor 
        ADD CONSTRAINT FK_usuIdCreador FOREIGN KEY (usu_intIdCreador)
        REFERENCES tbl_usuario(usu_intId) ;
        ");
    }
}

