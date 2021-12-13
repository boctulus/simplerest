<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblBodegaMaestro254 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_bodega (
        bod_intId INT(11) NOT NULL AUTO_INCREMENT,
        bod_varCodigo VARCHAR(50) NOT NULL,
        bod_varNombre VARCHAR(50) NOT NULL,
        bod_lonDescripcion LONGTEXT NOT NULL,
        bod_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
        bod_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
        est_intIdEstado INT(11) NOT NULL DEFAULT 1,
        usu_intIdCreador INT(11) NOT NULL,
        usu_intIdActualizador INT(11)  NULL,
        PRIMARY KEY (bod_intId)
        )
        ENGINE = INNODB,
        CHARACTER SET utf8,
        COLLATE utf8_general_ci;");

        Model::query("ALTER TABLE tbl_bodega ADD CONSTRAINT FK_bod_IdEstado FOREIGN KEY (est_intIdEstado)  REFERENCES tbl_estado (est_intId);");
        Model::query("ALTER TABLE tbl_bodega ADD CONSTRAINT FK_bod_idActualizador FOREIGN KEY (usu_intIdActualizador)  REFERENCES tbl_usuario (usu_intId);");
        Model::query("ALTER TABLE tbl_bodega ADD CONSTRAINT FK_bod_idCreador FOREIGN KEY (usu_intIdCreador)  REFERENCES tbl_usuario (usu_intId);");

    }
}

