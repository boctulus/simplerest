<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblEstadoMaestro1 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {


        $table = ('tbl_estado');
        $nom = 'est';

        $sc = (new Schema($table))

        ->setEngine('InnoDB')
        ->setCharset('utf8')
        ->setCollation('utf8_general_ci')

        ->integer($nom.'_intId')->auto()->pri()
        ->varchar($nom.'_varNombre', 100)->comment('hashed')
        ->varchar($nom.'_varIcono', 150)->comment('hashed')
        ->varchar($nom.'_varColor', 150)->comment('hashed')
        ->datetime($nom.'_dtimFechaCreacion')->default('current_timestamp')
        ->datetime($nom.'_dtimFechaActualizacion')->default('current_timestamp');

        $res = $sc->create();

        DB::table($table)->insert(
            array('est_varNombre'=>'Activo', 'est_varIcono'=> 'NA', 'est_varColor'=> 'NA')
        ); 

        DB::table($table)->insert(            
            array('est_varNombre'=>'Inactivo', 'est_varIcono'=> 'NA', 'est_varColor'=> 'NA')
        );   
        
        DB::table($table)->insert(            
            array('est_varNombre'=>'Rechazado', 'est_varIcono'=> 'NA', 'est_varColor'=> 'NA')
        );      
        
        DB::table($table)->insert(            
            array('est_varNombre'=>'Pendiente', 'est_varIcono'=> 'NA', 'est_varColor'=> 'NA')
        );      
        
        DB::table($table)->insert(            
            array('est_varNombre'=>'Terminado', 'est_varIcono'=> 'NA', 'est_varColor'=> 'NA')
        );      
        
        DB::table($table)->insert(            
            array('est_varNombre'=>'En Proceso', 'est_varIcono'=> 'NA', 'est_varColor'=> 'NA')
        );    

        DB::table($table)->insert(            
            array('est_varNombre'=>'Anulado', 'est_varIcono'=> 'NA', 'est_varColor'=> 'NA')
        );      

        DB::table($table)->insert(            
            array('est_varNombre'=>'Revision', 'est_varIcono'=> 'NA', 'est_varColor'=> 'NA')
        ); 

        DB::table($table)->insert(            
            array('est_varNombre'=>'Aprobado', 'est_varIcono'=> 'NA', 'est_varColor'=> 'NA')
        ); 

        
    }
}

