<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblNotaCreditoDetalle implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
           ///Nombre Tabla
           $table = ('tbl_nota_credito_detalle');

           ///Nomenclartura Tabla
           $nom = 'ncd_'; 
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
           ->date($nomDat.'Fecha')->comment('hashed')
           ->decimal($nomDec.'Valor' , 18,2)->comment('hashed')
           ->decimal($nomDec.'Cantidad' , 18,2)->comment('hashed')
           ->decimal($nomDec.'ValorTotal' , 18,2)->comment('hashed')
           ->decimal($nomDec.'PorcentajeIva', 18,2)->comment('hashed')
           ->decimal($nomDec.'ValorIva', 18,2)->comment('hashed')
           ->longtext($nomLon.'Descripcion')->comment('hashed')
      
           
           ///Campos tabla  constantes y foreign de otras tablas 
           ->datetime($nom.'dtimFechaCreacion')->default('current_timestamp')
           ->datetime($nom.'dtimFechaActualizacion')->default('"0000-00-00 00:00:00"')
           ->varchar('nct_varNroDocumento' ,20)
           ->integer('nct_intIdNotaCredito')
           ->integer('per_intIdPersona')
           ->integer('pro_intIdProducto')
           ->integer('doc_intIdDocumento')
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
   
           ///foreign --- references Tabla tbl_nota_credito
           ->foreign('nct_intIdNotaCredito')
           ->references('nct_intId')
           ->on('tbl_nota_credito')  
   
           ///foreign --- references Tabla tbl_documento
           ->foreign('doc_intIdDocumento')
           ->references('doc_intId')
           ->on('tbl_documento')  
   
           ///foreign --- references Tabla tbl_producto
           ->foreign('pro_intIdProducto')
           ->references('pro_intId')
           ->on('tbl_producto')  
   
           ///foreign --- references Tabla tbl_persona
           ->foreign('per_intIdPersona')
           ->references('per_intId')
           ->on('tbl_persona');
   
           //Creacion de tabla
           $res = $sc->create();
    }
}
