<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class TblCompras implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        ///Nombre Tabla
        $table = ('tbl_compras');

        ///Nomenclartura Tabla
        $nom = 'com';

        $sc = (new Schema($table))

        ->setEngine('InnoDB')
        ->setCharset('utf8')
        ->setCollation('utf8mb3_general_ci')

        ///Campos de tabla 
        ->integer($nom.'_intId')->auto()->pri()
        ->varchar($nom.'_varNroDocumento', 20)->comment('hashed')
        ->decimal($nom.'_decCantidadTotal', 18,2)->comment('hashed')
        ->decimal($nom.'_decBruto', 18,2)->comment('hashed')
        ->decimal($nom.'_decDescuento', 18.2)->comment('hashed')
        ->decimal($nom.'_decIva', 18,2)->comment('hashed')
        ->decimal($nom.'_decIca', 18,2)->comment('hashed')
        ->decimal($nom.'_decRetencion', 18,2)->comment('hashed')
        ->decimal($nom.'_decReteIva', 18,2)->comment('hashed')
        ->date($nom.'_datFecha')->comment('hashed')
        ->decimal($nom.'_decValorNeto', 18,2)->comment('hashed')
        ->date($nom.'_datFechaVencimiento')->comment('hashed')
        ->decimal($nom.'_decPorceRetefuente', 18,2)->comment('hashed')
        ->integer($nom.'_intTopeRetefuente')->comment('hashed')
        ->decimal($nom.'_decPorceReteiva', 18,2)->comment('hashed')
        ->integer($nom.'_intTopeReteiva')->comment('hashed')
        ->decimal($nom.'_decPorceIca', 18,2)->comment('hashed')
        ->integer($nom.'_intTopeReteIca')->comment('hashed')
        ->datetime($nom.'_dtimFechaCreacion')->default('current_timestamp')
        ->datetime($nom.'_dtimFechaActualizacion')->default('"0000-00-00 00:00:00"')
        ->longtext($nom.'_lonNota')->comment('hashed')
        ->varchar($nom.'_varFacturaProveedor', 100)->comment('hashed')
        ->integer('est_intIdEstado')->default('1')
        ->integer('doc_intIdDocumento')
        ->integer('cse_intIdConsecutivo')
        ->integer('per_intIdPersona')
        ->integer('usu_intIdCreador')
        ->integer('usu_intIdActualizador')->nullable()

        ///foreign --- references Tabla Estado
        ->foreign('est_intIdEstado')
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
        ->foreign('cse_intIdConsecutivo')
        ->references('cse_intId')
        ->on('tbl_consecutivo');

        //Creacion de tabla
        $res = $sc->create();
    }
}

