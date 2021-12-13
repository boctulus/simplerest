<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblEstudiosMaestro105 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
    

        $table = ('tbl_estudios');
        $nom = 'esd';

        $sc = (new Schema($table))

        ->setEngine('InnoDB')
        ->setCharset('utf8')
        ->setCollation('utf8_general_ci')

        ->integer($nom.'_intId')->auto()->pri()
        ->varchar($nom.'_varNombre')->comment('hashed')
        ->datetime($nom.'_dtimFechaCreacion')->default('current_timestamp')
        ->datetime($nom.'_dtimFechaActualizacion')->default("'0000-00-00'")
        ->integer('est_intEstado')->default('1')
        ->integer('usu_intIdCreador')
        ->integer('usu_intIdActualizador')->nullable();

        $users_table = 'tbl_estado';
        $users_pri   = 'est_intId';

        $sc->foreign('est_intEstado')->references($users_pri)->on($users_table);
        $sc->foreign('usu_intIdCreador')->references('usu_intId')->on('tbl_usuario');
        $sc->foreign('usu_intIdActualizador')->references('usu_intId')->on('tbl_usuario');

        $res = $sc->create();

        DB::table($table)->insert(
        array(
        $nom.'_varNombre'=>'ANALFABETA'
        ,'usu_intIdCreador'=>'1'
        )
        ); 

        DB::table($table)->insert(
        array(
        $nom.'_varNombre'=>'PRIMARIA'
        ,'usu_intIdCreador'=>'1'
        )
        ); 

        DB::table($table)->insert(
        array(
        $nom.'_varNombre'=>'SECUNDARIA'
        ,'usu_intIdCreador'=>'1'
        )
        ); 

        DB::table($table)->insert(
        array(
        $nom.'_varNombre'=>'TECNICO'
        ,'usu_intIdCreador'=>'1'
        )
        ); 

        DB::table($table)->insert(
        array(
        $nom.'_varNombre'=>'TECNOLOGO'
        ,'usu_intIdCreador'=>'1'
        )
        ); 

        DB::table($table)->insert(
        array(
        $nom.'_varNombre'=>'PROFESIONAL'
        ,'usu_intIdCreador'=>'1'
        )
        ); 

        DB::table($table)->insert(
        array(
        $nom.'_varNombre'=>'PREGRADO'
        ,'usu_intIdCreador'=>'1'
        )
        ); 

        DB::table($table)->insert(
        array(
        $nom.'_varNombre'=>'POSTGRADO'
        ,'usu_intIdCreador'=>'1'
        )
        ); 
    }
}

