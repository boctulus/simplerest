<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class TblContratoDetalle implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {

         ///Nombre Tabla
         $table = ('tbl_contrato_detalle');

         ///Nomenclartura Tabla
         $nom = 'cde_'; 
         $Int = $nom . 'int';
         $Var = $nom . 'var';
         $Dec = $nom . 'dec';
         $Lon = $nom . 'lon';
         $Dat = $nom . 'dat';
         $Bol = $nom . 'bol';
 
         $sc = (new Schema($table))
 
         ->setEngine('InnoDB')
         ->setCharset('utf8')
         ->setCollation('utf8mb3_general_ci')
         
         ///Campo primare key tabla 
         ->integer($Int.'Id')->auto()->pri()
 
         ///Campos tabla 
         ->varchar($Var.'NumeroContrato' , 50)->comment('hashed')
         ->decimal($Dec.'Valor' , 18,2)->comment('hashed')
         ->date($Dat.'FechaInicial')->comment('hashed')
         ->date($Dat.'FechaFinal')->comment('hashed')
         ->varchar($Var.'NumeroContrato' , 50)->comment('hashed')
         
         ///Campos tabla  constantes y foreign de otras tablas 
         ->datetime($nom.'dtimFechaCreacion')->default('current_timestamp')
         ->datetime($nom.'dtimFechaActualizacion')->default('"0000-00-00 00:00:00"')
         ->integer('ctr_intIdContrato')
         ->integer('pro_intIdProducto')
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

         ///foreign --- references Tabla tbl_contrato
         ->foreign('ctr_intIdContrato')
         ->references('ctr_intId')
         ->on('tbl_contrato')

         ///foreign --- references Tabla tbl_producto
         ->foreign('pro_intIdProducto')
         ->references('pro_intId')
         ->on('tbl_producto');
 
         //Creacion de tabla
         $res = $sc->create();
    
    }
}

