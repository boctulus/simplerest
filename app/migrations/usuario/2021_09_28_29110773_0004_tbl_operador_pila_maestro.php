<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class TblOperadorPilaMaestro9 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        
        $table = ('tbl_operador_pila');
        $nom = 'opp';

        $sc = (new Schema($table))

        ->setEngine('InnoDB')
        ->setCharset('utf8')
        ->setCollation('utf8_general_ci')

        ->integer($nom.'_intId')->auto()->pri()
        ->varchar($nom.'_varCodigo', 100)->comment('hashed')
        ->varchar($nom.'_varNombre', 100)->comment('hashed')
        ->longtext($nom.'_lonDescripcion')->comment('hashed')
        ->datetime($nom.'_dtimFechaCreacion')->default('current_timestamp')
        ->datetime($nom.'_dtimFechaActualizacion')->default('current_timestamp')
        ->integer('est_intEstado')->default('1')
        ->integer('usu_intIdCreador')
        ->integer('usu_intIdActualizador');

        $users_table = 'tbl_estado';
        $users_pri   = 'est_intId';

        $sc->foreign('est_intEstado')->references($users_pri)->on($users_table);

        $res = $sc->create();

        DB::table($table)->insert(
            array($nom.'_varCodigo'=>'01', $nom.'_varNombre'=>'SOI', $nom.'_lonDescripcion'=> 'SOI')
        ); 

        DB::table($table)->insert(
            array($nom.'_varCodigo'=>'02', $nom.'_varNombre'=>'MI PLANTILLA', $nom.'_lonDescripcion'=> 'MI PLANTILLA')
        ); 
        
        DB::table($table)->insert(
            array($nom.'_varCodigo'=>'03', $nom.'_varNombre'=>'APORTES EN LINEA', $nom.'_lonDescripcion'=> 'APORTES EN LINEA')
        ); 

        DB::table($table)->insert(
            array($nom.'_varCodigo'=>'04', $nom.'_varNombre'=>'ASOPAGOS', $nom.'_lonDescripcion'=> 'ASOPAGOS')
        ); 

        DB::table($table)->insert(
            array($nom.'_varCodigo'=>'05', $nom.'_varNombre'=>'FEDECAJAS (PILA FACIL)', $nom.'_lonDescripcion'=> 'FEDECAJAS (PILA FACIL)')
        ); 

        DB::table($table)->insert(
            array($nom.'_varCodigo'=>'06', $nom.'_varNombre'=>'SIMPLE', $nom.'_lonDescripcion'=> 'SIMPLE')
        ); 

        DB::table($table)->insert(
            array($nom.'_varCodigo'=>'07', $nom.'_varNombre'=>'ARUS (ENLACE OPERATIVO)', $nom.'_lonDescripcion'=> 'ARUS (ENLACE OPERATIVO)')
        ); 

    }
}

