<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblResolucionMaestro190 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_resolucion (
        res_intId INT(11) NOT NULL AUTO_INCREMENT,
        res_varNombreResolucion VARCHAR(100) NOT NULL ,
        res_intVigencia INT NOT NULL,
        res_datFechaInicial DATE NOT NULL,
        res_datFechaFinal INT NOT NULL,
        res_lonDescripcionResolucion LONGTEXT NOT NULL,
        res_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
        res_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
        est_intIdEstado INT(11) NOT NULL DEFAULT 1,
        usu_intIdCreador INT(11) NOT NULL,
        usu_intIdActualizador INT(11)  NULL,
        PRIMARY KEY (res_intId)
        )
        ENGINE = INNODB,
        CHARACTER SET utf8,
        COLLATE utf8_general_ci;");

        Model::query("ALTER TABLE tbl_resolucion   ADD UNIQUE INDEX res_varNombreResolucion(res_varNombreResolucion);");
        Model::query("ALTER TABLE tbl_resolucion ADD CONSTRAINT FK_res_IdEstado FOREIGN KEY (est_intIdEstado)  REFERENCES tbl_estado (est_intId);");     
        Model::query("ALTER TABLE tbl_resolucion   ADD CONSTRAINT FK_res_idActualizador FOREIGN KEY (usu_intIdActualizador) REFERENCES tbl_usuario(usu_intId);");
        Model::query("ALTER TABLE tbl_resolucion   ADD CONSTRAINT FK_res_idCreador FOREIGN KEY (usu_intIdCreador)    REFERENCES tbl_usuario(usu_intId);");

    }
}

