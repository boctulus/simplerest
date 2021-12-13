<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblPaisMaestro125 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_pais (
        pai_intId int(11) NOT NULL AUTO_INCREMENT,
        pai_varCodigo varchar(4) NOT NULL,
        pai_varPais varchar(100) NOT NULL,
        pai_varCodigoPaisCelular varchar(3) NOT NULL,
        pai_dtimFechaCreacion datetime NOT NULL DEFAULT CURRENT_TIMESTAMP(),
        pai_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
        est_intIdEstado int(11) NOT NULL DEFAULT 1,
        pai_intIdMoneda int(11) NOT NULL,
        usu_intIdCreador int(11) NOT NULL,
        usu_intIdActualizador int(11)  NULL,
        PRIMARY KEY (pai_intId),
        INDEX pai_intId (pai_intId),
        UNIQUE (pai_varCodigo)
        )
        ENGINE = INNODB,
        CHARACTER SET utf8,
        COLLATE utf8_general_ci,
        COMMENT = ' * Descripcion: Tabla para registrar los Diferentes Paises.
        * Author: http://www.divergente.net.co';");

        Model::query("ALTER TABLE tbl_pais ADD CONSTRAINT FK_pai_IdEstado FOREIGN KEY (est_intIdEstado)  REFERENCES tbl_estado (est_intId);");
        Model::query("ALTER TABLE tbl_pais ADD CONSTRAINT FK_pai_idActualizador FOREIGN KEY (usu_intIdActualizador)  REFERENCES tbl_usuario (usu_intId);");
        Model::query("ALTER TABLE tbl_pais ADD CONSTRAINT FK_pai_idCreador FOREIGN KEY (usu_intIdCreador)  REFERENCES tbl_usuario (usu_intId);");

        Model::query("ALTER TABLE tbl_pais
        ADD CONSTRAINT FK_pai_idMoneda FOREIGN KEY (pai_intIdMoneda)
        REFERENCES tbl_moneda (mon_intId);");


    }
}

