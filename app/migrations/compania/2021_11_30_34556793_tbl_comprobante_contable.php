<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblComprobanteContable implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        
        ///Nombre Tabla
        $table = ('tbl_comprobante_contable');

        ///Nomenclartura Tabla
        $nom = 'cmp';

        $sc = (new Schema($table))

        ->setEngine('InnoDB')
        ->setCharset('utf8')
        ->setCollation('utf8mb3_general_ci')

        ///Campos de tabla 
        ->integer($nom.'_intId')->auto()->pri()
        ->varchar($nom.'_varNroDocumento', 20)->comment('hashed')
        ->date($nom.'_datFechaMovimiento')->comment('hashed')
        ->decimal($nom.'_decTotalCuentaCredito', 18,2)->comment('hashed')
        ->decimal($nom.'_decTotalCuentaDebito', 18,2)->comment('hashed')
        ->decimal($nom.'_decTotalDiferencia', 18,2)->comment('hashed')
        ->longtext($nom.'_lonNota')->comment('hashed')
        ->datetime($nom.'_dtimFechaCreacion')->default('current_timestamp')
        ->datetime($nom.'_dtimFechaActualizacion')->default('"0000-00-00 00:00:00"')
        ->integer('est_intIdEstado')->default('1')
        ->integer('doc_intIdDocumento')
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

        ///foreign --- references Tabla Documento
        ->foreign('doc_intIdDocumento')
        ->references('doc_intId')
        ->on('tbl_documento');      

        //Creacion de tabla
        $res = $sc->create();
    }
}

