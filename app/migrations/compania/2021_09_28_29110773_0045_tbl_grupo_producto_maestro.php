<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class TblGrupoProductoMaestro223 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_grupo_producto (
        grp_intId INT(11) NOT NULL AUTO_INCREMENT,
        grp_varSiglaGrupoProducto VARCHAR(50) NOT NULL,
        grp_varDescripcionGrupo VARCHAR(50) NOT NULL,
        grp_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
        grp_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
        grp_intConsecutivoGrupoProducto INT(11) NOT NULL DEFAULT 0,
        est_intIdEstado INT(11) NOT NULL DEFAULT 1,
        cap_intIdCategoriaProducto INT(11) DEFAULT NULL,
        usu_intIdCreador INT(11) NOT NULL,
        usu_intIdActualizador INT(11)  NULL,
        PRIMARY KEY (grp_intId)
        )
        ENGINE = INNODB,
        CHARACTER SET utf8,
        COLLATE utf8_general_ci,
        COMMENT = '* Descripcion: 
        * Author: http://www.divergente.net.co';");

        Model::query("ALTER TABLE tbl_grupo_producto ADD CONSTRAINT FK_grp_IdEstado FOREIGN KEY (est_intIdEstado)  REFERENCES tbl_estado (est_intId);");
        Model::query("ALTER TABLE tbl_grupo_producto ADD CONSTRAINT FK_grp_idActualizador FOREIGN KEY (usu_intIdActualizador)  REFERENCES tbl_usuario (usu_intId);");
        Model::query("ALTER TABLE tbl_grupo_producto ADD CONSTRAINT FK_grp_idCreador FOREIGN KEY (usu_intIdCreador)  REFERENCES tbl_usuario (usu_intId);");
        Model::query("ALTER TABLE tbl_grupo_producto 
        ADD CONSTRAINT FK_grp_IdCategoriaProducto FOREIGN KEY (cap_intIdCategoriaProducto)
        REFERENCES tbl_categoria_producto(cap_intId) ON DELETE NO ACTION ON UPDATE NO ACTION;");
    }
}

