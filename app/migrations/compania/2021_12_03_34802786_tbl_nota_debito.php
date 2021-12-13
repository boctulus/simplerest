<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblNotaDebito implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
         ///Nombre Tabla
         $table = ('tbl_nota_debito');

         ///Nomenclartura Tabla
         $nom = 'nbt';
 
         $sc = (new Schema($table))
 
         ->setEngine('InnoDB')
         ->setCharset('utf8')
         ->setCollation('utf8mb3_general_ci')
 
         ///Campos de tabla 
         ->integer($nom.'_intId')->auto()->pri()
         ->varchar($nom.'_varNroDocumento', 20)->comment('hashed')
         ->decimal($nom.'_decCantidadTotal', 18,2)->comment('hashed')
         ->decimal($nom.'_decBruto', 18,2)->comment('hashed')
         ->decimal($nom.'_decDescuento', 18,2)->comment('hashed')
         ->decimal($nom.'_decIva', 18,2)->comment('hashed')
         ->decimal($nom.'_decIca', 18,2)->comment('hashed')
         ->decimal($nom.'_decRetencion', 18,2)->comment('hashed')
         ->decimal($nom.'_decReteIva', 18,2)->comment('hashed')
         ->date($nom.'_dateFecha')->comment('hashed')
         ->decimal($nom.'_decNeto', 18,2)->comment('hashed')
         ->decimal($nom.'_decPorceRetefuente', 18,2)->comment('hashed')
         ->integer($nom.'_intTopeRetefuente')->comment('hashed')
         ->decimal($nom.'_decPorceReteiva', 18,2)->comment('hashed')
         ->integer($nom.'_intTopeReteiva')->comment('hashed')
         ->decimal($nom.'_decPorceIca', 18,2)->comment('hashed')
         ->integer($nom.'_intTopeReteIca')->comment('hashed')
         ->datetime($nom.'_dtimFechaCreacion')->default('current_timestamp')
         ->datetime($nom.'_dtimFechaActualizacion')->default('"0000-00-00 00:00:00"')
         ->longtext($nom.'_lonNota')->comment('hashed')
         ->tinyint($nom.'_bolCruzado')->comment('hashed')
         ->varchar('nct_varNroDocumento' , 20)->comment('hashed')
         ->tinyint($nom.'_bolEnviadoDian')->comment('hashed')
         ->integer('nct_intIdNotaCredito')->comment('hashed')
         ->integer('cen_intIdCentrocostos')->comment('hashed')
         ->integer('doc_intIdDocumento')->comment('hashed')
         ->integer('cse_intIdConsecutivo')->comment('hashed')
         ->integer('per_intIdPersona')
         ->integer('usu_intIdCreador')
         ->integer('usu_intIdActualizador')->nullable() 
 
         ///foreign --- references Tabla tbl_nota_credito
         ->foreign('nct_intIdNotaCredito')
         ->references('nct_intId')
         ->on('tbl_nota_credito')

        ///foreign --- references Tabla tbl_centro_costos
        ->foreign('cen_intIdCentrocostos')
        ->references('cco_intId')
        ->on('tbl_centro_costos')

        ///foreign --- references Tabla tbl_documento
         ->foreign('doc_intIdDocumento')
         ->references('doc_intId')
         ->on('tbl_documento')

        ///foreign --- references Tabla tbl_consecutivo
        ->foreign('cse_intIdConsecutivo')
        ->references('cse_intId')
        ->on('tbl_consecutivo')

        ///foreign --- references Tabla tbl_persona
        ->foreign('per_intIdPersona')
        ->references('per_intId')
        ->on('tbl_persona')          
    
         ///foreign --- references Tabla tbl_usuario usu_intIdCreador
         ->foreign('usu_intIdCreador')
         ->references('usu_intId')
         ->on('tbl_usuario')

        ///foreign --- references Tabla tbl_usuario usu_intIdActualizador
        ->foreign('usu_intIdActualizador')
        ->references('usu_intId')
        ->on('tbl_usuario');      

 
         //Creacion de tabla
         $res = $sc->create();
    }
}

