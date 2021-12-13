<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblMvtoInventario implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        ///Nombre Tabla
        $table = ('tbl_mvto_inventario');

        ///Nomenclartura Tabla
        $nom = 'mvi_';

        $sc = (new Schema($table))

        ->setEngine('InnoDB')
        ->setCharset('utf8')
        ->setCollation('utf8mb3_general_ci')

        
        ///Campos de tabla 
        ->integer($nom.'intId')->auto()->pri()

        ->varchar($nom.'varNumeroDocumento', 20)->comment('hashed')
        ->decimal($nom.'decCantidadTotal', 18,2)->comment('hashed')
        ->decimal($nom.'decBruto' , 18,2)->comment('hashed')
        ->decimal($nom.'decDescuento' , 18,2)->comment('hashed')
        ->decimal($nom.'decIva' , 18,2)->comment('hashed')
        ->decimal($nom.'decIca' , 18,2)->comment('hashed')
        ->decimal($nom.'decRetencion' , 18,2)->comment('hashed')
        ->decimal($nom.'decReteIva' , 18,2)->comment('hashed')
        ->date($nom.'datFecha')->comment('hashed')
        ->decimal($nom.'decNeto' , 18,2)->comment('hashed')
        ->date($nom.'datFechaVencimiento')->comment('hashed')
        ->decimal($nom.'decPorceRetefuente' , 18,2)->comment('hashed')
        ->decimal($nom.'decNeto' , 18,2)->comment('hashed')
        ->integer($nom.'intTopeRetefuente')->comment('hashed')
        ->decimal($nom.'decPorceReteiva' , 18,2)->comment('hashed')
        ->integer($nom.'intTopeReteiva')->comment('hashed')
        ->decimal($nom.'decPorceIca' , 18,2)->comment('hashed')
        ->integer($nom.'intTopeReteIca')->comment('hashed')
        ->longtext($nom.'varNota')->comment('hashed')
        ->datetime($nom.'dtimFechaCreacion')->default('current_timestamp')
        ->datetime($nom.'dtimFechaActualizacion')->default('"0000-00-00 00:00:00"')
        ->integer('cen_intIdCentrocostos')
        ->integer('doc_intIdDocumento')
        ->integer('cse_intIdConsecutivo')
        ->integer('per_intIdPersona')
        ->integer('est_intIdEstado')->default('1')
        ->integer('usu_intIdCreador')
        ->integer('usu_intIdActualizador')->nullable() 

        ///foreign --- references Tabla tbl_estado
        ->foreign('est_intIdEstado')
        ->references('est_intId')
        ->on('tbl_estado')     
    
        ///foreign --- references Tabla tbl_usuario usu_intIdCreador
        ->foreign('usu_intIdCreador')
        ->references('usu_intId')
        ->on('tbl_usuario')

        ///foreign --- references Tabla tbl_usuario usu_intIdActualizador
        ->foreign('usu_intIdActualizador')
        ->references('usu_intId')
        ->on('tbl_usuario')  

        ///foreign --- references Tabla tbl_consecutivo
        ->foreign('cse_intIdConsecutivo')
        ->references('cse_intId')
        ->on('tbl_consecutivo')  
        
        ///foreign --- references Tabla tbl_documento
        ->foreign('doc_intIdDocumento')
        ->references('doc_intId')
        ->on('tbl_documento')  

        ///foreign --- references Tabla tbl_persona
        ->foreign('per_intIdPersona')
        ->references('per_intId')
        ->on('tbl_persona') 

        ///foreign --- references Tabla tbl_centro_costos
        ->foreign('cen_intIdCentrocostos')
        ->references('cco_intId')
        ->on('tbl_centro_costos');  

         //Creacion de tabla
         $res = $sc->create();


    }
}

