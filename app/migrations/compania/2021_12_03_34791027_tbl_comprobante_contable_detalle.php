<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class TblComprobanteContableDetalle implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
         ///Nombre Tabla
         $table = ('tbl_comprobante_contable_detalle');

         ///Nomenclartura Tabla
         $nom = 'cpd';
 
         $sc = (new Schema($table))
 
         ->setEngine('InnoDB')
         ->setCharset('utf8')
         ->setCollation('utf8mb3_general_ci')
 
         ///Campos de tabla 
         ->integer($nom.'_intId')->auto()->pri()
         ->varchar($nom.'_varCuentaContable', 20)->comment('hashed')
         ->varchar($nom.'_varTercero', 20)->comment('hashed')
         ->varchar($nom.'_varCentroCostos', 20)->comment('hashed')
         ->decimal($nom.'_decBase', 18,2)->comment('hashed')
         ->decimal($nom.'_decCuentaCredito', 18,2)->comment('hashed')
         ->decimal($nom.'_decCuentaDebito', 18,2)->comment('hashed')
         ->datetime($nom.'_dtimFechaCreacion')->default('current_timestamp')
         ->datetime($nom.'_dtimFechaActualizacion')->default('"0000-00-00 00:00:00"')
         ->integer('sub_intIdCuentaContable')
         ->integer('cmp_intIdComprobanteContable')
         ->integer('doc_intIdDocumento')
         ->integer('usu_intIdCreador')
         ->integer('usu_intIdActualizador')->nullable() 
 
         ///foreign --- references Tabla tbl_sub_cuenta_contable
         ->foreign('sub_intIdCuentaContable')
         ->references('sub_intId')
         ->on('tbl_sub_cuenta_contable')
         
         ///foreign --- references Tabla tbl_comprobante_contable
         ->foreign('cmp_intIdComprobanteContable')
         ->references('cmp_intId')
         ->on('tbl_comprobante_contable')
         
         ///foreign --- references Tabla tbl_documento
         ->foreign('doc_intIdDocumento')
         ->references('doc_intId')
         ->on('tbl_documento')
 
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

