<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;

class TblCargoMaestro17 implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
  
        $table = ('tbl_cargo');
        $nom = 'car';

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
        ->integer('emp_intIdEmpresa')->default('1')
        ->integer('est_intEstado')->default('1')
        ->integer('usu_intIdCreador')->default('1')
        ->integer('usu_intIdActualizador')->default('1');

        $users_table = 'tbl_estado';
        $users_pri   = 'est_intId';

        $sc->foreign('est_intEstado')->references($users_pri)->on($users_table);
        $sc->foreign('emp_intIdEmpresa')->references('emp_intId')->on('tbl_empresa');

        $res = $sc->create(true);

        DB::table($table)->insert(
            array(
                $nom.'_varCodigo'=>'NA'
                ,$nom.'_varNombre'=>'NO APLICA'
                ,$nom.'_lonDescripcion'=> 'NO APLICA'
            )
        ); 

   }
}

