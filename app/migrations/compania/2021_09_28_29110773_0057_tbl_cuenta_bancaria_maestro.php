<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblCuentaBancariaMaestro346 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_cuenta_bancaria (
        cba_intId INT(11) NOT NULL AUTO_INCREMENT,
        cba_varDescripcion VARCHAR(100) NOT NULL,
        cba_varNumeroCuenta VARCHAR(11) NOT NULL,
        cba_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
        cba_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
        est_intIdEstado_cba INT(11) NOT NULL DEFAULT 1,
        ban_intIdBanco INT(11) NOT NULL,
        tcb_intIdTipoCuentaBancaria INT(11) NOT NULL,
        emp_intIdEmpresa INT(11) NOT NULL,
        usu_intIdCreador INT(11) NOT NULL,
        usu_intIdActualizador INT(11)  NULL,
        PRIMARY KEY (cba_intId)
        )
        ENGINE = INNODB,
        CHARACTER SET utf8,
        COLLATE utf8_general_ci;
        ");

        Model::query("ALTER TABLE tbl_cuenta_bancaria 
        ADD CONSTRAINT FK_cba_IdActualizador FOREIGN KEY (usu_intIdActualizador)
        REFERENCES tbl_usuario(usu_intId);
        ");

        Model::query("ALTER TABLE tbl_cuenta_bancaria 
        ADD CONSTRAINT FK_cba_IdBanco FOREIGN KEY (ban_intIdBanco)
        REFERENCES tbl_banco(ban_intId);
        ");

        Model::query("ALTER TABLE tbl_cuenta_bancaria 
        ADD CONSTRAINT FK_cba_IdTipoCuentaBancaria FOREIGN KEY (tcb_intIdTipoCuentaBancaria)
        REFERENCES tbl_tipo_cuenta_bancaria(tcb_intId);
        ");

        Model::query("ALTER TABLE tbl_cuenta_bancaria 
        ADD CONSTRAINT FK_cba_IdEmpresa FOREIGN KEY (emp_intIdEmpresa)
        REFERENCES tbl_empresa(emp_intId);");

        Model::query("ALTER TABLE tbl_cuenta_bancaria 
        ADD CONSTRAINT FK_cba_IdEstado FOREIGN KEY (est_intIdEstado_cba)
        REFERENCES tbl_estado(est_intId);");

        Model::query("ALTER TABLE tbl_cuenta_bancaria 
        ADD CONSTRAINT FK_cba_IdUsuario FOREIGN KEY (usu_intIdCreador)
        REFERENCES tbl_usuario(usu_intId);");


    }
}

