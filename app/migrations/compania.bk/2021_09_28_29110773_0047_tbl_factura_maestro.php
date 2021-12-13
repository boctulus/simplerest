<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblFacturaMaestro232 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_factura (
        fac_intId INT(11) NOT NULL AUTO_INCREMENT,
        fac_varNroDocumento VARCHAR(20) NOT NULL,
        fac_decCantidadTotal DECIMAL(18, 2) NOT NULL,
        fac_decBruto DECIMAL(18, 2) NOT NULL,
        fac_decDescuento DECIMAL(18, 2) NOT NULL DEFAULT 0.00,
        fac_decIva DECIMAL(18, 2) NOT NULL,
        fac_decIca DECIMAL(18, 2) NOT NULL,
        fac_decRetencion DECIMAL(18, 2) NOT NULL,
        fac_decReteIva DECIMAL(18, 2) NOT NULL,
        fac_dateFecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        fac_decNeto DECIMAL(18, 2) NOT NULL,
        fac_bolEstado TINYINT(1) NOT NULL DEFAULT 1,
        fac_dateFechaVencimiento DATE NOT NULL,
        fac_decPorceRetefuente DECIMAL(10, 2) NOT NULL,
        fac_intTopeRetefuente INT(11) NOT NULL,
        fac_decPorceReteiva DECIMAL(10, 2) NOT NULL,
        fac_intTopeReteiva INT(11) NOT NULL,
        fac_decPorceIca DECIMAL(10, 2) NOT NULL,
        fac_intTopeReteIca INT(11) NOT NULL,
        fac_dtimFechaCreacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        fac_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
        fac_varNota LONGTEXT DEFAULT NULL,
        fac_bolPagado TINYINT(4) DEFAULT 0,
        cen_intIdCentrocostos INT(11) NOT NULL,
        doc_intDocumento INT(11) NOT NULL,
        cse_intIdConsecutivo INT(11) NOT NULL,
        per_intIdPersona INT(11) NOT NULL,
        usu_intIdCreador INT(11) NOT NULL,
        usu_intIdActualizador INT(11) NOT NULL,
        PRIMARY KEY (fac_intId)
        )
        ENGINE = INNODB,
        CHARACTER SET utf8,
        COLLATE utf8_general_ci;");

        Model::query("ALTER TABLE tbl_factura 
        ADD INDEX FK_cen_idCencos(cse_intIdConsecutivo);");

        Model::query("ALTER TABLE tbl_factura 
        ADD CONSTRAINT FK_fac_idActualizador FOREIGN KEY (usu_intIdActualizador)
        REFERENCES tbl_usuario(usu_intId);");

        Model::query("ALTER TABLE tbl_factura 
        ADD CONSTRAINT FK_fac_idConsecutivo FOREIGN KEY (cse_intIdConsecutivo)
        REFERENCES tbl_consecutivo(cse_intId);");

        Model::query("ALTER TABLE tbl_factura 
        ADD CONSTRAINT FK_fac_idCreador FOREIGN KEY (usu_intIdCreador)
        REFERENCES tbl_usuario(usu_intId);");

        Model::query("ALTER TABLE tbl_factura 
        ADD CONSTRAINT FK_fac_idDocumento FOREIGN KEY (doc_intDocumento)
        REFERENCES tbl_documento(doc_intId);");

        Model::query("ALTER TABLE tbl_factura 
        ADD CONSTRAINT FK_fac1_centro_costos FOREIGN KEY (cen_intIdCentrocostos)
        REFERENCES tbl_centro_costos(cco_intId);");

        Model::query("ALTER TABLE tbl_factura 
        ADD CONSTRAINT FK_fac_idPersona FOREIGN KEY (per_intIdPersona)
        REFERENCES tbl_persona(per_intId);");
    }
}

