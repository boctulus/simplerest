<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblGrupoEmpleado implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
            ///Nombre Tabla
            $table = ('tbl_grupo_empleado');

            ///Nomenclartura Tabla
            $nom = 'tge';
    
            $sc = (new Schema($table))
    
            ->setEngine('InnoDB')
            ->setCharset('utf8')
            ->setCollation('utf8mb3_general_ci')
    
            ///Campos de tabla 
            ->integer($nom.'_intId')->auto()->pri()
            ->varchar($nom.'_varCodigo', 100)->comment('hashed')
            ->varchar($nom.'_varNombre', 100)->comment('hashed')
            ->longtext($nom.'_lonDescripcion')->comment('hashed')
            ->datetime($nom.'_dtimFechaCreacion')->default('current_timestamp')
            ->datetime($nom.'_dtimFechaActualizacion')->default('"0000-00-00 00:00:00"')
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
           ->on('tbl_usuario');      
   
    
            //Creacion de tabla
            $res = $sc->create();
    }
}

