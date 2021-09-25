<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblProducto implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Model::query("CREATE TABLE tbl_producto (
  pro_intId INT(11) NOT NULL AUTO_INCREMENT,
  pro_varCodigoProducto VARCHAR(50) NOT NULL,
  pro_varNombreProducto VARCHAR(50) NOT NULL,
  pro_intCodigoBarras INT(11) NOT NULL,
  pro_intCostoCompra INT(11) NOT NULL,
  pro_intPrecioVenta INT(11) NOT NULL,
  pro_intStockMinimo INT(11) NOT NULL,
  pro_intSaldo INT(11) NOT NULL,
  pro_intStockMaximo INT(11) NOT NULL,
  pro_dtimFechaCreacion DATETIME NOT NULL DEFAULT current_timestamp(),
  pro_dtimFechaActualizacion DATETIME NULL DEFAULT NULL,
  est_intIdEstado INT(11) NOT NULL DEFAULT 1,
  sub_intIdCuentaContableCompra INT(11) NOT NULL,
  sub_intIdCuentaContableVenta INT(11) NOT NULL,
  mon_intIdMoneda INT(11) NOT NULL,
  iva_intIdIva INT(11) NOT NULL,
  unm_intIdUnidadMedida INT(11) NOT NULL,
  cap_intIdCategoriaProducto INT(11) NOT NULL,
  grp_intIdGrupoProducto INT(11) NOT NULL,
  usu_intIdCreador INT(11) NOT NULL,
  usu_intIdActualizador INT(11) NOT NULL,
  PRIMARY KEY (pro_intId)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;
");
    }
}

