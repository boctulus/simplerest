<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class TblCategoriaProductoMaestro219 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_categoria_producto (
        cap_intId INT(11) NOT NULL AUTO_INCREMENT,
        cap_varSiglaCategoriaProducto VARCHAR(50)  NULL,
        cap_varNombreCategoria VARCHAR(50) NOT NULL,
        cap_lonDescripcionCategoria LONGTEXT NULL,
        cap_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
        cap_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
        usu_intIdCreador INT(11) NOT NULL,
        usu_intIdActualizador INT(11)  NULL,
        est_intIdEstado INT(11) DEFAULT 1,
        PRIMARY KEY (cap_intId)
        )
        ENGINE = INNODB,
        CHARACTER SET utf8,
        COLLATE utf8_general_ci,
        COMMENT = '* Descripcion: 
        * Author: http://www.divergente.net.co';");


        Model::query("ALTER TABLE tbl_categoria_producto ADD CONSTRAINT FK_cap1_IdEstado FOREIGN KEY (est_intIdEstado)  REFERENCES tbl_estado (est_intId);");
        Model::query("ALTER TABLE tbl_categoria_producto ADD CONSTRAINT FK_cap1_idActualizador FOREIGN KEY (usu_intIdActualizador)  REFERENCES tbl_usuario (usu_intId);");
        Model::query("ALTER TABLE tbl_categoria_producto ADD CONSTRAINT FK_cap1_idCreador FOREIGN KEY (usu_intIdCreador)  REFERENCES tbl_usuario (usu_intId);");

    }
}

