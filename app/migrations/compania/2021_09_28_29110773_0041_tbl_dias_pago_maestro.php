<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class TblDiasPagoMaestro207 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_dias_pago (
        dpa_intId INT(11) NOT NULL AUTO_INCREMENT,
        dpa_intDiasPago INT(11) NOT NULL,
        dpa_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
        dpa_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
        est_intIdEstado INT(11) NOT NULL DEFAULT 1,
        usu_intIdCreador INT(11) NOT NULL,
        usu_intIdActualizador INT(11)  NULL,
        PRIMARY KEY (dpa_intId)
        )
        ENGINE = INNODB,
        CHARACTER SET utf8,
        COLLATE utf8_general_ci,
        COMMENT = ' * Descripcion: Tabla para registrar los diferentes dias o plazos de Pagos.
        * Author: http://www.divergente.net.co';");

        Model::query("ALTER TABLE tbl_dias_pago ADD CONSTRAINT FK_dpa_IdEstado FOREIGN KEY (est_intIdEstado)  REFERENCES tbl_estado (est_intId);");
        Model::query("ALTER TABLE tbl_dias_pago ADD CONSTRAINT FK_dpa_idActualizador FOREIGN KEY (usu_intIdActualizador)  REFERENCES tbl_usuario (usu_intId);");
        Model::query("ALTER TABLE tbl_dias_pago ADD CONSTRAINT FK_dpa_idCreador FOREIGN KEY (usu_intIdCreador)  REFERENCES tbl_usuario (usu_intId);");

    }
}

