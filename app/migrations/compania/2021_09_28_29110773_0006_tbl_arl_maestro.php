<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblArlMaestro6 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_arl (
        arl_intId int(11) NOT NULL AUTO_INCREMENT,
        arl_varCodigo varchar(100) NOT NULL,
        arl_varNombre varchar(100) NOT NULL,
        arl_dtimFechaCreacion datetime NOT NULL DEFAULT CURRENT_TIMESTAMP(),
        arl_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
        est_intIdEstado int(11) NOT NULL DEFAULT 1,
        usu_intIdCreador int(11) NOT NULL,
        usu_intIdActualizador int(11) NOT NULL,
        PRIMARY KEY (arl_intId)
        )
        ENGINE = INNODB,
        AUTO_INCREMENT = 1,
        CHARACTER SET utf8,
        COLLATE utf8_general_ci,
        COMMENT = ' * Descripcion: Tabla tbl_banco
        * Author: http://www.divergente.net.co';");
    }
}

