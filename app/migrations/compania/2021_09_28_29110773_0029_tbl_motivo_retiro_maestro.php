<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblMotivoRetiroMaestro146 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_motivo_retiro (
        mtr_intId int(11) NOT NULL AUTO_INCREMENT,
        mtr_varCodigo varchar(100)  NULL,
        mtr_varNombre varchar(100)  NOT NULL,
        mtr_lonDescripcion LONGTEXT  NULL,
        mtr_dtimFechaCreacion datetime NOT NULL DEFAULT CURRENT_TIMESTAMP(),
        mtr_dtimFechaActualizacion DATETIME  NOT NULL DEFAULT '1000-01-01 00:00:00',
        est_intIdEstado int(11) NOT NULL DEFAULT 1,
        usu_intIdCreador int(11) NOT NULL,
        usu_intIdActualizador int(11)  NULL,
        PRIMARY KEY (mtr_intId)
        )
        ENGINE = INNODB,
        CHARACTER SET utf8,
        COLLATE utf8_general_ci,
        COMMENT = ' * Descripcion: Tabla tbl_motivo_retiro 
        * Author: http://www.divergente.net.co';");

        Model::query("ALTER TABLE tbl_motivo_retiro ADD CONSTRAINT FK_mtr_IdEstado FOREIGN KEY (est_intIdEstado)  REFERENCES tbl_estado (est_intId);");
        Model::query("ALTER TABLE tbl_motivo_retiro ADD CONSTRAINT FK_mtr_idActualizador FOREIGN KEY (usu_intIdActualizador)  REFERENCES tbl_usuario (usu_intId);");
        Model::query("ALTER TABLE tbl_motivo_retiro ADD CONSTRAINT FK_mtr_idCreador FOREIGN KEY (usu_intIdCreador)  REFERENCES tbl_usuario (usu_intId);");

    }
}
