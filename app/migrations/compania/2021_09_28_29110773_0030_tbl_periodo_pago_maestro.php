<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class TblPeriodoPagoMaestro150 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_periodo_pago (
        pep_intId int(11) NOT NULL AUTO_INCREMENT,
        pep_varCodigo varchar(100)  NULL,
        pep_varNombre varchar(100) NOT NULL,
        pep_lonDescripcion longtext  NULL,
        pep_dtimFechaCreacion datetime NOT NULL DEFAULT CURRENT_TIMESTAMP(),
        pep_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
        est_intIdEstado int(11) NOT NULL DEFAULT 1,
        usu_intIdCreador int(11) NOT NULL,
        usu_intIdActualizador int(11)  NULL,
        PRIMARY KEY (pep_intId)
        )
        ENGINE = INNODB,
        CHARACTER SET utf8,
        COLLATE utf8_general_ci,
        COMMENT = ' * Descripcion: Tabla tbl_periodo_pago 
        * Author: http://www.divergente.net.co';");

        Model::query("ALTER TABLE tbl_periodo_pago ADD CONSTRAINT FK_pep_IdEstado FOREIGN KEY (est_intIdEstado)  REFERENCES tbl_estado (est_intId);");
        Model::query("ALTER TABLE tbl_periodo_pago ADD CONSTRAINT FK_pep_idActualizador FOREIGN KEY (usu_intIdActualizador)  REFERENCES tbl_usuario (usu_intId);");
        Model::query("ALTER TABLE tbl_periodo_pago ADD CONSTRAINT FK_pep_idCreador FOREIGN KEY (usu_intIdCreador)  REFERENCES tbl_usuario (usu_intId);");

        Model::query("INSERT INTO tbl_periodo_pago (pep_varNombre,usu_intIdCreador, usu_intIdActualizador)
        VALUES  ('DECADAL',1,1),
        ('QUINCENAL',1,1),
        ('MENSUAL',1,1)
        ;");

    }
}

