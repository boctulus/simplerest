<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblClaseLibretaMilitarMaestro120 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_clase_libreta_militar (
        clm_intId int(11) NOT NULL AUTO_INCREMENT,
        clm_varCodigo varchar(100)  NULL,
        clm_varNombre varchar(100) NOT NULL,
        clm_lonDescripcion LONGTEXT  NULL,
        clm_dtimFechaCreacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
        clm_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
        est_intIdEstado int(11) NOT NULL DEFAULT 1,
        usu_intIdCreador int(11) NOT NULL,
        usu_intIdActualizador int(11)  NULL,
        PRIMARY KEY (clm_intId)
        )
        ENGINE = INNODB,
        CHARACTER SET utf8,
        COLLATE utf8_general_ci,
        COMMENT = ' * Descripcion: Tabla tbl_clase_libreta_militar 
        * Author: http://www.divergente.net.co';");
        
        Model::query("ALTER TABLE tbl_clase_libreta_militar ADD CONSTRAINT FK_clm_IdEstado FOREIGN KEY (est_intIdEstado)  REFERENCES tbl_estado (est_intId);");
        Model::query("ALTER TABLE tbl_clase_libreta_militar ADD CONSTRAINT FK_clm_idActualizador FOREIGN KEY (usu_intIdActualizador)  REFERENCES tbl_usuario (usu_intId);");
        Model::query("ALTER TABLE tbl_clase_libreta_militar ADD CONSTRAINT FK_clm_idCreador FOREIGN KEY (usu_intIdCreador)  REFERENCES tbl_usuario (usu_intId);");

        Model::query("INSERT INTO tbl_clase_libreta_militar (clm_varNombre,usu_intIdCreador, usu_intIdActualizador)
        VALUES 
        ('PRIMERA CLASE', 1,1),
        ('SEGUNDA CLASE',  1,1);"
        );
    }
}

