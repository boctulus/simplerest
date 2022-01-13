<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class TblContrato implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
         ///Nombre Tabla
         $table = ('tbl_contrato');

         ///Nomenclartura Tabla
         $nom = 'ctr_'; 
         $nomInt = $nom . 'int';
         $nomVar = $nom . 'var';
         $nomDec = $nom . 'dec';
         $nomLon = $nom . 'lon';
         $nomDat = $nom . 'dat';
         $nomBol = $nom . 'bol';
 
         $sc = (new Schema($table))
 
         ->setEngine('InnoDB')
         ->setCharset('utf8')
         ->setCollation('utf8mb3_general_ci')
         
         ///Campo primare key tabla 
         ->integer($nomInt.'Id')->auto()->pri()
 
         ///Campos tabla 
         ->varchar($nomVar.'NumeroContrato' , 50)->comment('hashed')
         ->date($nomDat.'FechaInicial')->comment('hashed')
         ->date($nomDat.'FechaFinal')->comment('hashed')
         ->integer($nomInt.'MesesContrato')->comment('hashed')
         ->integer($nomInt.'DiasGracia')->comment('hashed')
         ->integer($nomInt.'TiempoAnalisis')->comment('hashed')  
         ->integer($nomInt.'TiempoRenovacion')->comment('hashed')
         ->integer($nomInt.'NumeroProductos')->comment('hashed')
         ->decimal($nomDec.'ValorMensual' , 18,2)->comment('hashed')
         ->longtext($nomLon.'Nota')->comment('hashed')
         
         ///Campos tabla  constantes y foreign de otras tablas 
         ->datetime($nom.'dtimFechaCreacion')->default('current_timestamp')
         ->datetime($nom.'dtimFechaActualizacion')->default('"0000-00-00 00:00:00"')
         ->integer('cco_intIdCentroCostos')
         ->integer('doc_intIdDocumento')
         ->integer('cse_intidConsecutivo')
         ->integer('est_intIdEstado')->default(1)
         ->integer('usu_intIdCreador')
         ->integer('usu_intIdActualizador')->nullable() 
     
         ///foreign --- references Tabla tbl_usuario usu_intIdCreador
         ->foreign('usu_intIdCreador')
         ->references('usu_intId')
         ->on('tbl_usuario')
 
         ///foreign --- references Tabla tbl_usuario usu_intIdActualizador
         ->foreign('usu_intIdActualizador')
         ->references('usu_intId')
         ->on('tbl_usuario')

         ///foreign --- references Tabla tbl_estado
         ->foreign('est_intIdEstado')
         ->references('est_intId')
         ->on('tbl_estado')
         
         ///foreign --- references Tabla tbl_centro_costos
         ->foreign('cco_intIdCentroCostos')
         ->references('cco_intId')
         ->on('tbl_centro_costos')
         
         ///foreign --- references Tabla tbl_documento
         ->foreign('doc_intIdDocumento')
         ->references('doc_intId')
         ->on('tbl_documento')

         ///foreign --- references Tabla tbl_consecutivo
         ->foreign('cse_intidConsecutivo')
         ->references('cse_intId')
         ->on('tbl_consecutivo')         ;
 
         //Creacion de tabla
         $res = $sc->create();
    }
}

