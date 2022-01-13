<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class TblPedido implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        ///Nombre Tabla
        $table = ('tbl_pedido');

        ///Nomenclartura Tabla
        $nom = 'ecp';

        $sc = (new Schema($table))

        ->setEngine('InnoDB')
        ->setCharset('utf8')
        ->setCollation('utf8mb3_general_ci')

         ///Campos de tabla 
        ->integer($nom.'_intId')->auto()->pri()
        ->varchar($nom.'_varNroDocumento', 20)->comment('hashed')
        ->decimal($nom.'_decCantidadTotal', 18,2)->comment('hashed')
        ->decimal($nom.'_decDescuento', 18.2)->comment('hashed')
        ->decimal($nom.'_decValorBruto', 18.2)->comment('hashed')
        ->decimal($nom.'_decIva', 18.2)->comment('hashed')
        ->decimal($nom.'_decRetefuente', 18.2)->comment('hashed')
        ->decimal($nom.'_decReteIca', 18.2)->comment('hashed')
        ->decimal($nom.'_decReteIva', 18.2)->comment('hashed')
        ->decimal($nom.'_decValorNeto', 18.2)->comment('hashed')
        ->date($nom.'_datFechaEmision')->comment('hashed')
        ->date($nom.'_datFechaVencimiento')->comment('hashed')
        ->longtext($nom.'_lonNota')->comment('hashed')
        ->datetime($nom.'_dtimFechaCreacion')->default('current_timestamp')
        ->datetime($nom.'_dtimFechaActualizacion')->default('"0000-00-00 00:00:00"')
        ->integer('per_intIdPersona')
        ->integer('doc_intIdDocumento')
        ->integer('cen_intIdCentrocostos')
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

        ///foreign --- references Tabla Persona
        ->foreign('per_intIdPersona')
        ->references('per_intId')
        ->on('tbl_persona')  

        ///foreign --- references Tabla Documento
        ->foreign('doc_intIdDocumento')
        ->references('doc_intId')
        ->on('tbl_documento')        
        
        ///foreign --- references Tabla Centro Costos
        ->foreign('cen_intIdCentrocostos')
        ->references('cco_intId')
        ->on('tbl_centro_costos');

        //Creacion de tabla
        $res = $sc->create();

     

        
    }
}

