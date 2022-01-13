<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class TblProductoMaestro315 implements IMigration
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
        pro_intSaldo INT(11) NOT NULL DEFAULT 0,
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

        Model::query("ALTER TABLE tbl_producto 
        ADD UNIQUE INDEX pro_varCodigoProducto(pro_varCodigoProducto);");

        Model::query("ALTER TABLE tbl_producto 
        ADD CONSTRAINT FK_pro_IdActualizador FOREIGN KEY (usu_intIdActualizador) REFERENCES tbl_usuario(usu_intId);");

        Model::query("ALTER TABLE tbl_producto 
        ADD CONSTRAINT FK_pro_IdCreador FOREIGN KEY (usu_intIdCreador) REFERENCES tbl_usuario(usu_intId);");

        Model::query("ALTER TABLE tbl_producto 
        ADD CONSTRAINT FK_pro_IdEstado FOREIGN KEY (est_intIdEstado)
        REFERENCES tbl_estado(est_intId);"); 

        Model::query("ALTER TABLE tbl_producto 
        ADD CONSTRAINT FK_pro_IdIva FOREIGN KEY (iva_intIdIva)
        REFERENCES tbl_iva(iva_intId); ");

        Model::query("ALTER TABLE tbl_producto 
        ADD CONSTRAINT FK_pro_IdMoneda FOREIGN KEY (mon_intIdMoneda)
        REFERENCES tbl_moneda(mon_intId);");

        Model::query("ALTER TABLE tbl_producto 
        ADD CONSTRAINT FK_pro_IdUnidadMedida FOREIGN KEY (unm_intIdUnidadMedida)
        REFERENCES tbl_unidad_medida(unm_intId);");

        Model::query("ALTER TABLE tbl_producto 
        ADD CONSTRAINT FK_producto_categoria_producto FOREIGN KEY (cap_intIdCategoriaProducto)
        REFERENCES tbl_categoria_producto(cap_intId);");

        Model::query("ALTER TABLE tbl_producto 
        ADD CONSTRAINT FK_producto_grupo_producto FOREIGN KEY (grp_intIdGrupoProducto)
        REFERENCES tbl_grupo_producto(grp_intId);");

        Model::query("ALTER TABLE tbl_producto 
        ADD CONSTRAINT FK_producto_sub_cuenta_contable FOREIGN KEY (sub_intIdCuentaContableCompra)
        REFERENCES tbl_sub_cuenta_contable(sub_intId);");

        Model::query("ALTER TABLE tbl_producto 
        ADD CONSTRAINT FK_producto_sub_cuenta_contable_2 FOREIGN KEY (sub_intIdCuentaContableVenta)
        REFERENCES tbl_sub_cuenta_contable(sub_intId);");

        
    }
}

