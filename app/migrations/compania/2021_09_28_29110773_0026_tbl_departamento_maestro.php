<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class TblDepartamentoMaestro130 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_departamento (
        dep_intId int(11) NOT NULL AUTO_INCREMENT,
        dep_varCodigoDepartamento varchar(50) NOT NULL,
        dep_varDepartamento varchar(100) NOT NULL,
        dep_dtimFechaCreacion datetime NOT NULL DEFAULT CURRENT_TIMESTAMP(),
        dep_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
        est_intIdEstado int(11) NOT NULL DEFAULT 1,
        pai_intIdPais int(11) NOT NULL,
        usu_intIdCreador int(11) NOT NULL,
        usu_intIdActualizador int(11)  NULL,
        PRIMARY KEY (dep_intId)
        )
        ENGINE = INNODB,
        CHARACTER SET utf8,
        COLLATE utf8_general_ci,
        COMMENT = ' * Descripcion: Tabla para registrar los Departamentos.
        * Author: http://www.divergente.net.co';");

        Model::query("ALTER TABLE tbl_departamento ADD CONSTRAINT FK_dep_IdEstado FOREIGN KEY (est_intIdEstado)  REFERENCES tbl_estado (est_intId);");
        Model::query("ALTER TABLE tbl_departamento ADD CONSTRAINT FK_dep_idActualizador FOREIGN KEY (usu_intIdActualizador)  REFERENCES tbl_usuario (usu_intId);");
        Model::query("ALTER TABLE tbl_departamento ADD CONSTRAINT FK_dep_idCreador FOREIGN KEY (usu_intIdCreador)  REFERENCES tbl_usuario (usu_intId);");

        Model::query("ALTER TABLE tbl_departamento
        ADD CONSTRAINT FK_dep_idPais FOREIGN KEY (pai_intIdPais)
        REFERENCES tbl_pais (pai_intId);");
    }
}

