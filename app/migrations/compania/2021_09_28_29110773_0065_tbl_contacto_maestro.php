<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class TblContactoMaestro372 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_contacto (
        con_intId INT(11) NOT NULL AUTO_INCREMENT,
        con_varNombreContacto VARCHAR(250) NOT NULL,
        con_varEmail VARCHAR(100) NOT NULL,
        con_varCelular VARCHAR(15) NOT NULL,
        con_varDireccion VARCHAR(250) DEFAULT NULL,
        con_varTelefono VARCHAR(10) DEFAULT NULL,
        con_varExtension VARCHAR(5) DEFAULT NULL,
        con_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
        con_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
        est_intIdEstado INT(11) NOT NULL DEFAULT 0,
        emp_intIdEmpresa INT(11) NOT NULL,
        car_intIdcargo INT(11) NOT NULL,
        ciu_intIdCiudad INT(11) NOT NULL,
        pai_intIdPais INT(11) NOT NULL,
        usu_intIdCreador INT(11) NOT NULL,
        usu_intIdActualizador INT(11) NOT NULL,
        PRIMARY KEY (con_intId)
        )
        ENGINE = INNODB,
        CHARACTER SET utf8,
        COLLATE utf8_general_ci;");

        Model::query("ALTER TABLE tbl_contacto 
        ADD CONSTRAINT FK_con_empresa FOREIGN KEY (emp_intIdEmpresa)
        REFERENCES tbl_empresa(emp_intId);");

        Model::query("ALTER TABLE tbl_contacto 
        ADD CONSTRAINT FK_con_idActualizador FOREIGN KEY (usu_intIdActualizador)
        REFERENCES tbl_usuario(usu_intId);");

        Model::query("ALTER TABLE tbl_contacto 
        ADD CONSTRAINT FK_con_idCargo FOREIGN KEY (car_intIdcargo)
        REFERENCES tbl_cargo(car_intId);
        ");

        Model::query("ALTER TABLE tbl_contacto 
        ADD CONSTRAINT FK_con_idCiudad FOREIGN KEY (ciu_intIdCiudad)
        REFERENCES tbl_ciudad(ciu_intId);");

        Model::query("ALTER TABLE tbl_contacto 
        ADD CONSTRAINT FK_con_idCreador FOREIGN KEY (usu_intIdCreador)
        REFERENCES tbl_usuario(usu_intId);");

        Model::query("ALTER TABLE tbl_contacto 
        ADD CONSTRAINT FK_con_idPais FOREIGN KEY (pai_intIdPais)
        REFERENCES tbl_pais(pai_intId);");

        Model::query("ALTER TABLE tbl_contacto 
        ADD CONSTRAINT FK_con_idestado FOREIGN KEY (est_intIdEstado)
        REFERENCES tbl_estado(est_intId) ;");
    }
}

