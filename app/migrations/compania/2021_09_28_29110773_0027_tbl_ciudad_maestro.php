<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class TblCiudadMaestro135 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_ciudad (
        ciu_intId int(11) NOT NULL AUTO_INCREMENT,
        ciu_varCodigo varchar(5) NOT NULL,
        ciu_varCiudad varchar(100) NOT NULL,
        ciu_varIndicativoTelefono varchar(3) NOT NULL,
        ciu_dtimFechaCreacion datetime NOT NULL DEFAULT CURRENT_TIMESTAMP(),
        ciu_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
        est_intIdEstado int(11) NOT NULL DEFAULT 1,
        pai_intIdPais int(11) NOT NULL,
        dep_intIdDepartamento int(11) NOT NULL,
        usu_intIdCreador int(11) NOT NULL,
        usu_intIdActualizador int(11)  NULL,
        PRIMARY KEY (ciu_intId),
        INDEX ciu_intId (ciu_intId),
        UNIQUE(ciu_varCodigo)
        )
        ENGINE = INNODB,
        CHARACTER SET utf8,
        COLLATE utf8_general_ci,
        COMMENT = ' * Descripcion: Tabla para registrar los diferentes Ciudades.
        * Author: http://www.divergente.net.co';");

        Model::query("ALTER TABLE tbl_ciudad ADD CONSTRAINT FK_ciu_IdEstado FOREIGN KEY (est_intIdEstado)  REFERENCES tbl_estado (est_intId);");
        Model::query("ALTER TABLE tbl_ciudad ADD CONSTRAINT FK_ciu_idActualizador FOREIGN KEY (usu_intIdActualizador)  REFERENCES tbl_usuario (usu_intId);");
        Model::query("ALTER TABLE tbl_ciudad ADD CONSTRAINT FK_ciu_idCreador FOREIGN KEY (usu_intIdCreador)  REFERENCES tbl_usuario (usu_intId);");

        Model::query("ALTER TABLE tbl_ciudad
        ADD CONSTRAINT FK_ciu_idDepartamento FOREIGN KEY (dep_intIdDepartamento)
        REFERENCES tbl_departamento (dep_intId);");

        Model::query("ALTER TABLE tbl_ciudad
        ADD CONSTRAINT FK_ciu_idPais FOREIGN KEY (pai_intIdPais)
        REFERENCES tbl_pais (pai_intId);");
    }
}

