<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblPersonaMaestro169 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_persona (
        per_intId INT(11) NOT NULL AUTO_INCREMENT,
        per_varIdentificacion VARCHAR(20) NOT NULL,
        per_varDV VARCHAR(1) NOT NULL,
        per_varRazonSocial VARCHAR(200) DEFAULT NULL,
        per_varNombre VARCHAR(100) DEFAULT NULL,
        per_varNombre2 VARCHAR(100) DEFAULT NULL,
        per_varApellido VARCHAR(100) DEFAULT NULL,
        per_varApellido2 VARCHAR(100) DEFAULT NULL,
        per_varNombreCompleto MEDIUMTEXT NOT NULL,
        per_varDireccion VARCHAR(255) NOT NULL,
        per_varCelular VARCHAR(15) NOT NULL,
        per_varTelefono VARCHAR(15) DEFAULT NULL,
        per_varEmail VARCHAR(100) NOT NULL,
        per_varMatriculaMercantil VARCHAR(100) NULL,
        per_datFechaNacimiento DATE NOT NULL,
        per_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
        per_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
        tpr_intIdTipoPersona INT(11) NOT NULL,
        pai_intIdPaisNacimiento INT(11) NOT NULL,
        ciu_intIdCiudadNacimiento INT(11) NOT NULL,
        dep_intIdDepartamentoNacimiento INT(11) NOT NULL,
        gen_intIdGenero INT(11) NOT NULL  ,
        tid_intIdTipoDocumento INT(11) NOT NULL,
        est_intIdEstado INT(11) NOT NULL DEFAULT 1,
        usu_intIdCreador INT(11) NOT NULL,
        usu_intIdActualizador INT(11)  NULL,
        PRIMARY KEY (per_intId)
        )
        ENGINE = INNODB,
        CHARACTER SET utf8,
        COLLATE utf8_general_ci;");

        Model::query("ALTER TABLE tbl_persona 
        ADD CONSTRAINT FK_per_idActualizador FOREIGN KEY (usu_intIdActualizador)
        REFERENCES tbl_usuario(usu_intId);
        ");

        Model::query("ALTER TABLE tbl_persona 
        ADD CONSTRAINT FK_per_idTipoDocumento FOREIGN KEY (tid_intIdTipoDocumento)
        REFERENCES tbl_tipo_documento(tid_intId);");

        Model::query("ALTER TABLE tbl_persona 
        ADD CONSTRAINT FK_per_idCiudad FOREIGN KEY (ciu_intIdCiudadNacimiento)
        REFERENCES tbl_ciudad(ciu_intId);
        ");

        Model::query("ALTER TABLE tbl_persona 
        ADD CONSTRAINT FK_per_idDepartamento FOREIGN KEY (dep_intIdDepartamentoNacimiento)
        REFERENCES tbl_departamento(dep_intId);
        ");

        Model::query("ALTER TABLE tbl_persona 
        ADD CONSTRAINT FK_per_idCreador FOREIGN KEY (usu_intIdCreador)
        REFERENCES tbl_usuario(usu_intId);
        ");

        Model::query("ALTER TABLE tbl_persona 
        ADD CONSTRAINT FK_per_idEstado FOREIGN KEY (est_intIdEstado)
        REFERENCES tbl_estado(est_intId);
        ");

        Model::query("ALTER TABLE tbl_persona 
        ADD CONSTRAINT FK_per_idGenero FOREIGN KEY (gen_intIdGenero)
        REFERENCES tbl_genero(gen_intId);");

        Model::query("ALTER TABLE tbl_persona 
        ADD CONSTRAINT FK_per_idPais FOREIGN KEY (pai_intIdPaisNacimiento)
        REFERENCES tbl_pais(pai_intId);");

        Model::query("ALTER TABLE tbl_persona 
        ADD CONSTRAINT FK_per_idTipoPersona FOREIGN KEY (tpr_intIdTipoPersona)
        REFERENCES tbl_tipo_persona(tpr_intId) ON DELETE NO ACTION ON UPDATE NO ACTION;");
        


    }
}

