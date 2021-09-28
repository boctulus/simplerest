<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblFacturaDetalleMaestro332 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_factura_detalle (
  fde_intId INT(11) NOT NULL AUTO_INCREMENT,
  fde_dateFecha DATE NOT NULL,
  fde_decValor DECIMAL(18, 2) NOT NULL,
  fde_bolEstado TINYINT(1) NOT NULL DEFAULT 1,
  fde_decCantidad DECIMAL(18, 2) NOT NULL,
  fde_decValorTotal DECIMAL(18, 2) NOT NULL,
  fde_decPorcentajeIva DECIMAL(18, 2) NOT NULL,
  fde_decValorIva DECIMAL(18, 2) NOT NULL,
  fde_decPorcentajeDescuento DECIMAL(18, 2) NOT NULL,
  fde_decValorDescuento DECIMAL(18, 2) NOT NULL,
  fde_decPorcentajeRetefuente DECIMAL(18, 2) NOT NULL,
  fde_decValorRetefuente DECIMAL(18, 2) NOT NULL,
  fde_decPorcentajeReteIva DECIMAL(18, 2) NOT NULL,
  fde_decValorReteIva DECIMAL(18, 2) NOT NULL,
  fde_decPorcentajeReteIca DECIMAL(18, 2) NOT NULL,
  fde_decValorReteIca DECIMAL(18, 2) NOT NULL,
  fde_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
  fde_dtimFechaActualizacion DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  fac_intNroDocumento VARCHAR(20) NOT NULL,
  fde_varDescripcion LONGTEXT NOT NULL,
  fac_intIdFactura INT(11) NOT NULL,
  pro_intIdProducto INT(11) NOT NULL,
  doc_intIdDocumento INT(11) NOT NULL,
  fde_intIdBodega INT(11) NOT NULL DEFAULT 1,
  usu_intIdCreador INT(11) NOT NULL,
  usu_intIdActualizador INT(11) NOT NULL,
  PRIMARY KEY (fde_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;
");
    }
}

