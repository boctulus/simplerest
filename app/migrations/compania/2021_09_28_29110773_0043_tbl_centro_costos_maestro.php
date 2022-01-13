<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class TblCentroCostosMaestro215 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_centro_costos (
        cco_intId INT(11) NOT NULL AUTO_INCREMENT,
        cco_varCodigo VARCHAR(100) NOT NULL,
        cco_varCentroCostos VARCHAR(100) NOT NULL,
        cco_lonDescripcion LONGTEXT NOT NULL,
        cco_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
        cco_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
        est_intIdEstado INT(11) NOT NULL DEFAULT 1,
        usu_intIdCreador INT(11) NOT NULL,
        usu_intIdActualizador INT(11)  NULL,
        PRIMARY KEY (cco_intId)
        )
        ENGINE = INNODB,
        CHARACTER SET utf8,
        COLLATE utf8_general_ci,
        COMMENT = ' * Descripcion: Tabla para registrar los diferentes Centro de Costos.
        * Author: http://www.divergente.net.co';");

        Model::query("ALTER TABLE tbl_centro_costos   ADD UNIQUE INDEX cco_varCodigo(cco_varCodigo);");
        Model::query("ALTER TABLE tbl_centro_costos ADD CONSTRAINT FK_cco_IdEstado FOREIGN KEY (est_intIdEstado)  REFERENCES tbl_estado (est_intId);");
        Model::query("ALTER TABLE tbl_centro_costos ADD CONSTRAINT FK_cco_idActualizador FOREIGN KEY (usu_intIdActualizador)  REFERENCES tbl_usuario (usu_intId);");
        Model::query("ALTER TABLE tbl_centro_costos ADD CONSTRAINT FK_cco_idCreador FOREIGN KEY (usu_intIdCreador)  REFERENCES tbl_usuario (usu_intId);");

    }
}
