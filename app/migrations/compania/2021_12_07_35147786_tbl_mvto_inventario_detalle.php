<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class TblMvtoInventarioDetalle implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        ///Nombre Tabla
        $table = ('tbl_mvto_inventario_detalle');

        ///Nomenclartura Tabla
        $nom = 'mvd_';
        $nomInt = $nom . 'int';
        $nomVar = $nom . 'var';
        $nomDec = $nom . 'dec';
        $nomLon = $nom . 'lon';
        $nomDat = $nom . 'dat';

        $sc = (new Schema($table))

        ->setEngine('InnoDB')
        ->setCharset('utf8')
        ->setCollation('utf8mb3_general_ci')
        
        ///Campo primare key tabla 
        ->integer($nomInt.'Id')->auto()->pri()

        ///Campos tabla 
        ->varchar($nom.'varNumeroDocumento', 20)->comment('hashed')
        ->varchar($nomVar.'Descripcion', 100)->comment('hashed')
        ->date($nomDat.'Fecha')->comment('hashed')
        ->decimal($nomDec.'Cantidad' , 18,2)->comment('hashed')
        ->decimal($nomDec.'Valor', 18,2)->comment('hashed')
        ->decimal($nomDec.'Iva', 18,2)->comment('hashed')
        ->decimal($nomDec.'PorceIVA', 18,2)->comment('hashed')
        ->decimal($nomDec.'PorcentajeDescuento', 18,2)->comment('hashed')
        ->decimal($nomDec.'ValorTotal', 18,2)->comment('hashed')
        ->longtext($nomLon.'Nota')->comment('hashed')

        ///Campos tabla  constantes y foreign de otras tablas 
        ->datetime($nom.'dtimFechaCreacion')->default('current_timestamp')
        ->datetime($nom.'dtimFechaActualizacion')->default('"0000-00-00 00:00:00"')
        ->integer('bod_intIdBodega')
        ->integer('pro_intIdProducto')
        ->integer('mvi_intIdMvtoInventario')
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

        ///foreign --- references Tabla tbl_bodega
        ->foreign('bod_intIdBodega')
        ->references('bod_intId')
        ->on('tbl_bodega')  
        
        ///foreign --- references Tabla tbl_producto
        ->foreign('pro_intIdProducto')
        ->references('pro_intId')
        ->on('tbl_producto')  

        ///foreign --- references Tabla tbl_persona
        ->foreign('per_intIdPersona')
        ->references('per_intId')
        ->on('tbl_persona') 

        ///foreign --- references Tabla tbl_mvto_inventario
        ->foreign('mvi_intIdMvtoInventario')
        ->references('mvi_intId')
        ->on('tbl_mvto_inventario');

        //Creacion de tabla
        $res = $sc->create();

      
  
    }
}

