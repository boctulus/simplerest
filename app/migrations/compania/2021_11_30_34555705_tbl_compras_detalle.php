<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblComprasDetalle implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        ///Nombre Tabla
        $table = ('tbl_compras_detalle');

        ///Nomenclartura Tabla
        $nom = 'cmd';

        $sc = (new Schema($table))

        ->setEngine('InnoDB')
        ->setCharset('utf8')
        ->setCollation('utf8mb3_general_ci')

            
        ///Campos de tabla 
        ->integer($nom.'_intId')->auto()->pri()
        ->varchar($nom.'_varNroDocumento', 20)->comment('hashed')
        ->date($nom.'_datFecha')->comment('hashed')
        ->decimal($nom.'_decCantidad', 18,2)->comment('hashed')
        ->decimal($nom.'_decValor', 18.2)->comment('hashed')
        ->decimal($nom.'_decIva', 18,2)->comment('hashed')
        ->decimal($nom.'_decPorceIva', 18,2)->comment('hashed')
        ->decimal($nom.'_decPorcentajeDescuento', 18,2)->comment('hashed')
        ->decimal($nom.'_decValorTotal', 18,2)->comment('hashed')
        ->longtext($nom.'_lonNota')->comment('hashed')
        ->varchar('oco_varNumeroOC', 20)->comment('hashed')
        ->integer('oco_intIdOC')
        ->integer('doc_intDocumentoOC')
        ->datetime($nom.'_dtimFechaCreacion')->default('current_timestamp')
        ->datetime($nom.'_dtimFechaActualizacion')->default('"0000-00-00 00:00:00"')
        ->integer('com_intIdCompras')
        ->integer('pro_intIdProducto')
        ->integer('per_intIdPersona')
        ->integer('doc_intIdDocumento')
        ->integer('bod_intIdBodega')
        ->integer('cen_intIdCentrocostos')
        ->integer('usu_intIdCreador')
        ->integer('usu_intIdActualizador')->nullable()

        ///foreign --- references Tabla Compras
        ->foreign('com_intIdCompras')
        ->references('com_intId')
        ->on('tbl_compras')
        
        ///foreign --- references Tabla Usuario Creador
        ->foreign('usu_intIdCreador')
        ->references('usu_intId')
        ->on('tbl_usuario')
        
        ///foreign --- references Tabla Usuario Actualizador
        ->foreign('usu_intIdActualizador')
        ->references('usu_intId')
        ->on('tbl_usuario')

        ///foreign --- references Tabla Persona
        ->foreign('per_intIdPersona')
        ->references('per_intId')
        ->on('tbl_persona')  

        ///foreign --- references Tabla Documento
        ->foreign('doc_intIdDocumento')
        ->references('doc_intId')
        ->on('tbl_documento')        
        
        ///foreign --- references Tabla Producto
        ->foreign('pro_intIdProducto')
        ->references('pro_intId')
        ->on('tbl_producto')

         
        ///foreign --- references Tabla Bodega
        ->foreign('bod_intIdBodega')
        ->references('bod_intId')
        ->on('tbl_bodega')

        ///foreign --- references Tabla Centro Costos
        ->foreign('cen_intIdCentrocostos')
        ->references('cco_intId')
        ->on('tbl_centro_costos');

        //Creacion de tabla
        $res = $sc->create();
    }
}

