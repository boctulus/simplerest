<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblDescuentoMaestro211 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_descuento (
        des_intId INT(11) NOT NULL AUTO_INCREMENT,
        des_varDescuento VARCHAR(100) NOT NULL,
        des_decDescuento DECIMAL(18, 2) NOT NULL,
        des_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
        des_timFechaActualizacion DATETIME NULL DEFAULT NULL,
        est_intIdEstado INT(11) NOT NULL DEFAULT 1,
        usu_intIdCreador INT(11) NOT NULL,
        usu_intIdActualizador INT(11)  NULL,
        PRIMARY KEY (des_intId)
        )
        ENGINE = INNODB,
        CHARACTER SET utf8,
        COLLATE utf8_general_ci,
        COMMENT = ' * Descripcion: Tabla para registrar los diferentes descuentos de los alidos.
        * Author: http://www.divergente.net.co';");

        Model::query("ALTER TABLE tbl_descuento   ADD UNIQUE INDEX des_varDescuento(des_varDescuento);");
        Model::query("ALTER TABLE tbl_descuento   ADD UNIQUE INDEX des_decDescuento(des_decDescuento);");
        Model::query("ALTER TABLE tbl_descuento ADD CONSTRAINT FK_des_IdEstado FOREIGN KEY (est_intIdEstado)  REFERENCES tbl_estado (est_intId);");
        Model::query("ALTER TABLE tbl_descuento ADD CONSTRAINT FK_des_idActualizador FOREIGN KEY (usu_intIdActualizador)  REFERENCES tbl_usuario (usu_intId);");
        Model::query("ALTER TABLE tbl_descuento ADD CONSTRAINT FK_des_idCreador FOREIGN KEY (usu_intIdCreador)  REFERENCES tbl_usuario (usu_intId);");

    }
}

