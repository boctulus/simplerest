<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblNovedadesNomina implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
          ///Nombre Tabla
          $table = ('tbl_novedades_nomina');

          ///Nomenclartura Tabla
          $nom = 'nvn_'; 
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
          ->varchar($nomVar.'Codigo' , 100)->comment('hashed')
          ->varchar($nom.'Nombre' , 100)->comment('hashed')
          ->longtext($nomLon.'Descripcion')->comment('hashed')
          ->date($nomDat.'Fecha')->comment('hashed')
          ->decimal($nomDec.'Cantidad' , 18,2)->comment('hashed')
          ->decimal($nomDec.'Valor', 18,2)->comment('hashed')     
          
          ///Campos tabla  constantes y foreign de otras tablas 
          ->datetime($nom.'dtimFechaCreacion')->default('current_timestamp')
          ->datetime($nom.'dtimFechaActualizacion')->default('"0000-00-00 00:00:00"')
          ->integer('tce_intIdContrato')
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
          
          ///foreign --- references Tabla tbl_contrato_empleado
          ->foreign('tce_intIdContrato')
          ->references('tce_intId')
          ->on('tbl_contrato_empleado');
  
          //Creacion de tabla
          $res = $sc->create();
    }
}

