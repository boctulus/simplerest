<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class TblPedidoDetalle implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        ///Nombre Tabla
        $table = ('tbl_pedido_detalle');

        ///Nomenclartura Tabla
        $nom = 'dtp';

        $sc = (new Schema($table))

        ->setEngine('InnoDB')
        ->setCharset('utf8')
        ->setCollation('utf8mb3_general_ci')

         ///Campos de tabla 
        ->integer($nom.'_intId')->auto()->pri()
        ->varchar($nom.'_varNroDocumento', 20)->comment('hashed')
        ->decimal($nom.'_decCantidad', 18.2)->comment('hashed')
        ->decimal($nom.'_decPrecioUnitario', 18.2)->comment('hashed')
        ->decimal($nom.'_decValor', 18.2)->comment('hashed')
        ->decimal($nom.'_decDescuento', 18.2)->comment('hashed')
        ->decimal($nom.'_decPorDescuento', 18.2)->comment('hashed')
        ->decimal($nom.'_decIva', 18.2)->comment('hashed')
        ->decimal($nom.'_decPorcentajeIva', 18.2)->comment('hashed')
        ->decimal($nom.'_decRetefuente', 18.2)->comment('hashed')
        ->decimal($nom.'_decPorcentajeRetefuente', 18.2)->comment('hashed')
        ->decimal($nom.'_decReteIca', 18.2)->comment('hashed')
        ->decimal($nom.'_decPorcentajeReteIca', 18.2)->comment('hashed')
        ->decimal($nom.'_decReteIva', 18.2)->comment('hashed')
        ->decimal($nom.'_decPorcentajeReteiva', 18.2)->comment('hashed')
        ->decimal($nom.'_decNeto', 18.2)->comment('hashed')
        ->varchar($nom.'_varDescripcionProducto', 500)->comment('hashed')
        ->date($nom.'_datFechaEmision')->comment('hashed')
        ->date($nom.'_datFechaVencimiento')->comment('hashed')
        ->longtext($nom.'_lonNota')->comment('hashed')
        ->datetime($nom.'_dtimFechaCreacion')->default('current_timestamp')
        ->datetime($nom.'_dtimFechaActualizacion')->default('"0000-00-00 00:00:00"')
        ->integer('ecp_intIdPedido')
        ->integer('pro_intIdProducto')
        ->integer('fde_intIdBodega')
        ->integer('doc_intIdDocumento')
        ->integer('est_intEstado')->default('1')
        ->integer('usu_intIdCreador')
        ->integer('usu_intIdActualizador')->nullable()

        ///foreign --- references Tabla Estado
        ->foreign('est_intEstado')
        ->references('est_intId')
        ->on('tbl_estado')
        
        ///foreign --- references Tabla Usuario Creador
        ->foreign('usu_intIdCreador')
        ->references('usu_intId')
        ->on('tbl_usuario')
        
        ///foreign --- references Tabla Usuario Actualizador
        ->foreign('usu_intIdActualizador')
        ->references('usu_intId')
        ->on('tbl_usuario')

        ///foreign --- references Tabla tbl_encabe_pedido
        ->foreign('ecp_intIdPedido')
        ->references('ecp_intId')
        ->on('tbl_pedido')   

        ///foreign --- references Tabla tbl_producto
        ->foreign('pro_intIdProducto')
        ->references('pro_intId')
        ->on('tbl_producto')   

        ///foreign --- references Tabla tbl_bodega
        ->foreign('fde_intIdBodega')
        ->references('bod_intId')
        ->on('tbl_bodega')  

        ///foreign --- references Tabla Documento
        ->foreign('doc_intIdDocumento')
        ->references('doc_intId')
        ->on('tbl_documento');
        
     
        //Creacion de tabla
        $res = $sc->create();

     
    }
}

